<?php

namespace App\Livewire\Admin;

use App\Models\SocialNetwork;
use Livewire\Component;

class SocialLinksManager extends Component
{
    public $socialLinks = [];
    public $availableNetworks = [];
    public $existingLinks = [];

    public function mount($existingLinks = [])
    {
        // Initialize with existing links or empty array
        $this->existingLinks = $existingLinks;

        if (!empty($existingLinks)) {
            $this->socialLinks = $existingLinks;
        } else {
            $this->socialLinks = [
                ['social_network_id' => '', 'url' => '']
            ];
        }
    }

    public function addSocialLink()
    {
        $this->socialLinks[] = ['social_network_id' => '', 'url' => ''];
    }

    public function removeSocialLink($index)
    {
        unset($this->socialLinks[$index]);
        $this->socialLinks = array_values($this->socialLinks);

        // Ensure at least one empty field exists
        if (empty($this->socialLinks)) {
            $this->socialLinks = [['social_network_id' => '', 'url' => '']];
        }
    }

    public function render()
    {
        // Load available social networks
        $this->availableNetworks = SocialNetwork::active()->ordered()->get();

        return view('livewire.admin.social-links-manager');
    }
}
