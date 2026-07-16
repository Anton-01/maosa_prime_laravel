<?php

namespace App\Services\Admin;

use App\Models\AdminMenu;
use App\Models\AdminMenuItem;
use Illuminate\Support\Collection;

class MenuBuilderService
{
    public const TOP_LEVEL_PARENT_ID = 0;

    /**
     * All menus with their items flattened in display order
     * (parents followed by their children).
     */
    public function list(): Collection
    {
        return AdminMenu::with('items')
            ->orderBy('id')
            ->get()
            ->map(fn (AdminMenu $menu) => [
                'id' => $menu->id,
                'name' => $menu->name,
                'items' => $this->flattenItems($menu->items),
            ]);
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function createMenu(array $data): AdminMenu
    {
        return AdminMenu::create(['name' => $data['name']]);
    }

    public function deleteMenu(int $id): void
    {
        // Items are removed by the FK cascade.
        AdminMenu::findOrFail($id)->delete();
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function createItem(array $data): AdminMenuItem
    {
        $parentId = (int) ($data['parent_id'] ?? self::TOP_LEVEL_PARENT_ID);

        return AdminMenuItem::create([
            'menu_id' => $data['menu_id'],
            'label' => $data['label'],
            'link' => $data['link'],
            'class' => $data['class'] ?? null,
            'parent_id' => $parentId,
            'depth' => $this->depthForParent($parentId),
            'sort' => $this->nextSort((int) $data['menu_id'], $parentId),
        ]);
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function updateItem(int $id, array $data): AdminMenuItem
    {
        $item = AdminMenuItem::findOrFail($id);

        $parentId = (int) ($data['parent_id'] ?? self::TOP_LEVEL_PARENT_ID);

        // An item cannot be its own parent.
        if ($parentId === $item->id) {
            $parentId = self::TOP_LEVEL_PARENT_ID;
        }

        $parentChanged = $parentId !== $item->parent_id;

        $item->update([
            'label' => $data['label'],
            'link' => $data['link'],
            'class' => $data['class'] ?? null,
            'parent_id' => $parentId,
            'depth' => $this->depthForParent($parentId),
            'sort' => $parentChanged ? $this->nextSort($item->menu_id, $parentId) : $item->sort,
        ]);

        if ($parentChanged) {
            $this->syncChildrenDepth($item);
        }

        return $item;
    }

    public function deleteItem(int $id): void
    {
        $item = AdminMenuItem::findOrFail($id);

        // Re-attach children to the top level so they stay visible.
        AdminMenuItem::where('parent_id', $item->id)->update([
            'parent_id' => self::TOP_LEVEL_PARENT_ID,
            'depth' => 0,
        ]);

        $item->delete();
    }

    /**
     * Swap sort order with the previous/next sibling.
     */
    public function moveItem(int $id, string $direction): void
    {
        $item = AdminMenuItem::findOrFail($id);

        $siblingQuery = AdminMenuItem::where('menu_id', $item->menu_id)
            ->where('parent_id', $item->parent_id);

        $sibling = $direction === 'up'
            ? $siblingQuery->where('sort', '<', $item->sort)->orderByDesc('sort')->first()
            : $siblingQuery->where('sort', '>', $item->sort)->orderBy('sort')->first();

        if (! $sibling) {
            return;
        }

        [$item->sort, $sibling->sort] = [$sibling->sort, $item->sort];

        $item->save();
        $sibling->save();
    }

    /**
     * Flatten the item tree in display order (recursive by parent).
     *
     * @return array<int, array<string, mixed>>
     */
    private function flattenItems(Collection $items, int $parentId = self::TOP_LEVEL_PARENT_ID): array
    {
        return $items
            ->where('parent_id', $parentId)
            ->sortBy('sort')
            ->values()
            ->flatMap(function (AdminMenuItem $item) use ($items) {
                $row = [
                    'id' => $item->id,
                    'label' => $item->label,
                    'link' => $item->link,
                    'class' => $item->class,
                    'parentId' => $item->parent_id,
                    'depth' => $item->depth,
                    'sort' => $item->sort,
                ];

                return [$row, ...$this->flattenItems($items, $item->id)];
            })
            ->all();
    }

    private function depthForParent(int $parentId): int
    {
        if ($parentId === self::TOP_LEVEL_PARENT_ID) {
            return 0;
        }

        return (int) AdminMenuItem::findOrFail($parentId)->depth + 1;
    }

    private function nextSort(int $menuId, int $parentId): int
    {
        return (int) AdminMenuItem::where('menu_id', $menuId)
            ->where('parent_id', $parentId)
            ->max('sort') + 1;
    }

    /**
     * Keep descendant depths consistent after moving an item.
     */
    private function syncChildrenDepth(AdminMenuItem $item): void
    {
        AdminMenuItem::where('parent_id', $item->id)
            ->get()
            ->each(function (AdminMenuItem $child) use ($item) {
                $child->update(['depth' => $item->depth + 1]);
                $this->syncChildrenDepth($child);
            });
    }
}
