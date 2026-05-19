@extends('frontend.layouts.guest-home')

@push('styles')
<style>
/* ============================================================
   GUEST HOME — Full-screen split layout
   Offshore oil & gas platform — night scene SVG
   ============================================================ */
.ghero-wrapper {
    display: flex;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

/* ---- Left panel ---- */
.ghero-left {
    flex: 1 1 0;
    position: relative;
    background: #060d28;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    min-width: 0;
}

.ghero-platform-svg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}

/* Gradient overlay at bottom of left panel */
.ghero-left::after {
    content: '';
    position: absolute;
    left: 0; right: 0; bottom: 0;
    height: 40%;
    background: linear-gradient(to top, rgba(6,13,40,0.92) 0%, transparent 100%);
    pointer-events: none;
    z-index: 1;
}

/* Floating badges */
.ghero-badge {
    position: absolute;
    z-index: 3;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    padding: 11px 20px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.28);
    animation: gheroBadgeFloat 3.6s ease-in-out infinite;
}
.ghero-badge i  { color: var(--colorPrimary); font-size: 17px; }
.ghero-badge span { font-weight: 600; color: #1a1f5e; font-size: 13px; white-space: nowrap; }
.ghero-badge-1  { top: 20%; right: 9%; animation-delay: 0s; }
.ghero-badge-2  { bottom: 30%; left: 6%; animation-delay: 1.9s; }

@keyframes gheroBadgeFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-11px); }
}

/* Bottom label */
.ghero-left-label {
    position: relative;
    z-index: 2;
    padding: 0 38px 30px;
}
.ghero-left-label p {
    margin: 0;
    font-size: 12.5px;
    color: rgba(180,205,255,0.55);
    line-height: 1.75;
}

/* ---- Right panel ---- */
.ghero-right {
    flex: 0 0 42%;
    background: #ded9f3;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 50px 48px;
    overflow-y: auto;
    min-width: 0;
}

/* Welcome pill */
.ghero-welcome-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: linear-gradient(135deg,#f5f3ff,#ede9fe);
    color: var(--colorPrimary);
    padding: 8px 17px;
    border-radius: 50px;
    font-size: 12.5px;
    font-weight: 700;
    margin-bottom: 20px;
    align-self: flex-start;
}
.ghero-welcome-pill i { font-size: 11px; }

/* Title */
.ghero-title {
    font-size: clamp(22px, 3vw, 32px);
    font-weight: 800;
    color: #1a1f5e;
    line-height: 1.22;
    margin-bottom: 14px;
}
.ghero-title b,
.ghero-title strong { color: var(--colorPrimary); font-weight: 800; }

/* Subtitle */
.ghero-subtitle {
    font-size: 14.5px;
    color: #777;
    line-height: 1.72;
    margin-bottom: 30px;
}
.ghero-subtitle p { margin: 0; }

/* CTA */
.ghero-cta { margin-bottom: 30px; }
.ghero-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--colorPrimary);
    color: #fff;
    padding: 13px 30px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: filter .25s, transform .2s, box-shadow .25s;
    box-shadow: 0 5px 16px rgba(0,0,0,0.16);
}
.ghero-btn:hover {
    color: #fff;
    text-decoration: none;
    filter: brightness(1.12);
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.2);
}

/* Feature list */
.ghero-features { display: flex; flex-direction: column; gap: 13px; }
.ghero-feature  { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #555; }
.ghero-feature i { color: #22c55e; font-size: 15px; flex-shrink: 0; }

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 1100px) {
    .ghero-right { flex: 0 0 44%; padding: 40px 36px; }
}

@media (max-width: 860px) {
    .ghero-wrapper   { flex-direction: column; }
    .ghero-left      { flex: 0 0 230px; height: 230px; }
    .ghero-left-label,
    .ghero-badge-2   { display: none; }
    .ghero-badge-1   { top: auto; bottom: 14px; right: 14px; animation: none; }
    .ghero-right     { flex: 1 1 0; width: 100%; padding: 30px 28px; justify-content: flex-start; padding-top: 28px; }
    .ghero-welcome-pill { margin-bottom: 14px; }
}

@media (max-width: 560px) {
    .ghero-left  { flex: 0 0 180px; height: 180px; }
    .ghero-badge { display: none; }
    .ghero-right { padding: 22px 20px; }
    .ghero-btn   { width: 100%; justify-content: center; }
}

@media (max-width: 380px) {
    .ghero-left  { display: none; }
    .ghero-right { justify-content: center; }
}
</style>
@endpush

@section('contents')
<div class="ghero-wrapper">

    {{-- ======== LEFT PANEL — Offshore Oil & Gas Platform (Night Scene) ======== --}}
    <div class="ghero-left">

        {{-- SVG: Offshore drilling platform at night --}}
        <svg xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 580 560"
             preserveAspectRatio="xMidYMid slice"
             class="ghero-platform-svg"
             aria-hidden="true">
            <defs>
                <linearGradient id="ghSky" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#020818"/>
                    <stop offset="55%"  stop-color="#0a1440"/>
                    <stop offset="100%" stop-color="#111c5c"/>
                </linearGradient>
                <linearGradient id="ghOcean" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#0b1748"/>
                    <stop offset="45%"  stop-color="#071030"/>
                    <stop offset="100%" stop-color="#040c20"/>
                </linearGradient>
                <linearGradient id="ghDeck" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#1e2e7c"/>
                    <stop offset="100%" stop-color="#142264"/>
                </linearGradient>
                <radialGradient id="ghMoon" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="#fce96a"/>
                    <stop offset="70%"  stop-color="#f5ca18"/>
                    <stop offset="100%" stop-color="#e6b000"/>
                </radialGradient>
                <radialGradient id="ghMoonHalo" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="#f5c842" stop-opacity="0.35"/>
                    <stop offset="100%" stop-color="#f5c842" stop-opacity="0"/>
                </radialGradient>
                <radialGradient id="ghFlare" cx="50%" cy="0%" r="100%">
                    <stop offset="0%"   stop-color="#ff8c00"/>
                    <stop offset="55%"  stop-color="#ff5000" stop-opacity="0.65"/>
                    <stop offset="100%" stop-color="#ff3300" stop-opacity="0"/>
                </radialGradient>
                <linearGradient id="ghMoonRef" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#f5c842" stop-opacity="0.22"/>
                    <stop offset="100%" stop-color="#f5c842" stop-opacity="0"/>
                </linearGradient>
                <linearGradient id="ghFlareRef" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#ff6600" stop-opacity="0.28"/>
                    <stop offset="100%" stop-color="#ff6600" stop-opacity="0"/>
                </linearGradient>
                <linearGradient id="ghBotFade" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#040c20" stop-opacity="0"/>
                    <stop offset="100%" stop-color="#020810" stop-opacity="0.9"/>
                </linearGradient>
                <filter id="ghGlow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="2.5" result="b"/>
                    <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
                <filter id="ghSoftGlow" x="-60%" y="-60%" width="220%" height="220%">
                    <feGaussianBlur stdDeviation="5" result="b"/>
                    <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
            </defs>

            {{-- Sky --}}
            <rect width="580" height="560" fill="url(#ghSky)"/>

            {{-- Stars --}}
            <g opacity="0.88">
                <circle cx="20"  cy="18"  r="1"   fill="white"/>
                <circle cx="52"  cy="40"  r="1.4" fill="white"/>
                <circle cx="88"  cy="12"  r="1"   fill="white"/>
                <circle cx="115" cy="55"  r="1"   fill="white"/>
                <circle cx="148" cy="22"  r="1.2" fill="white"/>
                <circle cx="175" cy="68"  r="1"   fill="white"/>
                <circle cx="208" cy="15"  r="1.4" fill="white"/>
                <circle cx="242" cy="42"  r="1"   fill="white"/>
                <circle cx="275" cy="9"   r="1"   fill="white"/>
                <circle cx="305" cy="58"  r="1.2" fill="white"/>
                <circle cx="38"  cy="75"  r="1"   fill="white"/>
                <circle cx="70"  cy="95"  r="1"   fill="white"/>
                <circle cx="100" cy="70"  r="1.2" fill="white"/>
                <circle cx="135" cy="112" r="1"   fill="white"/>
                <circle cx="165" cy="88"  r="1"   fill="white"/>
                <circle cx="192" cy="120" r="1"   fill="white"/>
                <circle cx="222" cy="98"  r="1.2" fill="white"/>
                <circle cx="255" cy="135" r="1"   fill="white"/>
                <circle cx="288" cy="78"  r="1"   fill="white"/>
                <circle cx="318" cy="118" r="1"   fill="white"/>
                <circle cx="50"  cy="150" r="1"   fill="white"/>
                <circle cx="82"  cy="142" r="1.2" fill="white"/>
                <circle cx="112" cy="165" r="1"   fill="white"/>
                <circle cx="18"  cy="36"  r="0.8" fill="white" opacity="0.65"/>
                <circle cx="44"  cy="58"  r="0.8" fill="white" opacity="0.55"/>
                <circle cx="74"  cy="28"  r="0.8" fill="white" opacity="0.75"/>
                <circle cx="102" cy="85"  r="0.8" fill="white" opacity="0.55"/>
                <circle cx="132" cy="30"  r="0.8" fill="white" opacity="0.70"/>
                <circle cx="160" cy="100" r="0.8" fill="white" opacity="0.50"/>
                <circle cx="188" cy="46"  r="0.8" fill="white" opacity="0.85"/>
                <circle cx="220" cy="72"  r="0.8" fill="white" opacity="0.60"/>
                <circle cx="252" cy="108" r="0.8" fill="white" opacity="0.70"/>
                <circle cx="278" cy="56"  r="0.8" fill="white" opacity="0.80"/>
                <circle cx="308" cy="32"  r="0.8" fill="white" opacity="0.60"/>
                <circle cx="338" cy="82"  r="0.8" fill="white" opacity="0.70"/>
                <circle cx="60"  cy="165" r="0.8" fill="white" opacity="0.50"/>
                <circle cx="95"  cy="180" r="0.8" fill="white" opacity="0.60"/>
            </g>

            {{-- Shooting stars --}}
            <line x1="115" y1="25"  x2="155" y2="52"  stroke="white" stroke-width="1.5" stroke-linecap="round" opacity="0.70"/>
            <line x1="113" y1="25"  x2="118" y2="28"  stroke="white" stroke-width="2.5" stroke-linecap="round" opacity="0.90"/>
            <line x1="218" y1="18"  x2="246" y2="37"  stroke="white" stroke-width="1"   stroke-linecap="round" opacity="0.50"/>

            {{-- Moon halo --}}
            <circle cx="448" cy="72" r="62" fill="url(#ghMoonHalo)"/>
            {{-- Moon --}}
            <circle cx="448" cy="72" r="36" fill="url(#ghMoon)" filter="url(#ghSoftGlow)"/>
            {{-- Moon craters --}}
            <circle cx="438" cy="62"  r="6"   fill="#daa800" opacity="0.28"/>
            <circle cx="458" cy="82"  r="4.5" fill="#daa800" opacity="0.22"/>
            <circle cx="446" cy="88"  r="3"   fill="#daa800" opacity="0.18"/>

            {{-- Distant tanker ship on horizon --}}
            <g opacity="0.30">
                <rect x="22" y="348" width="95"  height="10" fill="#1a2870" rx="2"/>
                <rect x="30" y="338" width="10"  height="12" fill="#1a2870"/>
                <rect x="45" y="330" width="7"   height="18" fill="#1a2870"/>
                <rect x="58" y="334" width="6"   height="14" fill="#1a2870"/>
                <rect x="80" y="340" width="8"   height="10" fill="#1a2870"/>
            </g>

            {{-- Ocean / sea --}}
            <rect y="355" width="580" height="205" fill="url(#ghOcean)"/>

            {{-- Ocean surface waves --}}
            <path d="M0,355 Q36,348 72,355 Q108,362 144,355 Q180,348 216,355 Q252,362 288,355 Q324,348 360,355 Q396,362 432,355 Q468,348 504,355 Q542,362 580,355 L580,365 Q544,370 510,365 Q474,360 438,365 Q400,370 364,365 Q326,360 290,365 Q252,370 216,365 Q178,360 142,365 Q104,370 68,365 Q32,360 0,365 Z"
                  fill="#1a2870" opacity="0.55"/>
            <path d="M0,372 Q50,366 100,372 Q150,378 200,372 Q250,366 300,372 Q350,378 400,372 Q450,366 500,372 Q542,378 580,372 L580,382 L0,382 Z"
                  fill="#152268" opacity="0.42"/>
            <path d="M0,388 Q80,382 160,388 Q240,394 320,388 Q400,382 480,388 Q530,392 580,388 L580,398 L0,398 Z"
                  fill="#111e5c" opacity="0.28"/>

            {{-- Moon reflection on water --}}
            <ellipse cx="448" cy="415" rx="18"  ry="55" fill="url(#ghMoonRef)"/>
            {{-- Flare reflection on water --}}
            <ellipse cx="550" cy="418" rx="8"   ry="38" fill="url(#ghFlareRef)"/>

            {{-- ======== PLATFORM LEGS ======== --}}
            <rect x="78"  y="210" width="14" height="150" fill="#0e1852"/>
            <rect x="78"  y="210" width="3"  height="150" fill="#1a2870" opacity="0.38"/>
            <rect x="194" y="210" width="14" height="150" fill="#0e1852"/>
            <rect x="194" y="210" width="3"  height="150" fill="#1a2870" opacity="0.38"/>
            <rect x="366" y="210" width="14" height="150" fill="#0e1852"/>
            <rect x="366" y="210" width="3"  height="150" fill="#1a2870" opacity="0.38"/>
            <rect x="488" y="210" width="14" height="150" fill="#0e1852"/>
            <rect x="488" y="210" width="3"  height="150" fill="#1a2870" opacity="0.38"/>

            {{-- X-bracing between legs (3 levels each span) --}}
            {{-- L1–L2 --}}
            <line x1="92"  y1="220" x2="194" y2="278" stroke="#152268" stroke-width="2.5"/>
            <line x1="92"  y1="278" x2="194" y2="220" stroke="#152268" stroke-width="2.5"/>
            <line x1="92"  y1="283" x2="194" y2="333" stroke="#152268" stroke-width="2.5"/>
            <line x1="92"  y1="333" x2="194" y2="283" stroke="#152268" stroke-width="2.5"/>
            {{-- L2–L3 --}}
            <line x1="208" y1="220" x2="366" y2="278" stroke="#152268" stroke-width="2.5"/>
            <line x1="208" y1="278" x2="366" y2="220" stroke="#152268" stroke-width="2.5"/>
            <line x1="208" y1="283" x2="366" y2="333" stroke="#152268" stroke-width="2.5"/>
            <line x1="208" y1="333" x2="366" y2="283" stroke="#152268" stroke-width="2.5"/>
            {{-- L3–L4 --}}
            <line x1="380" y1="220" x2="488" y2="278" stroke="#152268" stroke-width="2.5"/>
            <line x1="380" y1="278" x2="488" y2="220" stroke="#152268" stroke-width="2.5"/>
            <line x1="380" y1="283" x2="488" y2="333" stroke="#152268" stroke-width="2.5"/>
            <line x1="380" y1="333" x2="488" y2="283" stroke="#152268" stroke-width="2.5"/>

            {{-- Horizontal connecting beam --}}
            <rect x="78"  y="288" width="424" height="5" fill="#152268"/>

            {{-- ======== MAIN DECK ======== --}}
            <rect x="56"  y="182" width="468" height="8"  fill="#1e2e7a" rx="1"/>
            <rect x="58"  y="188" width="464" height="5"  fill="#253888" opacity="0.7"/>
            <rect x="58"  y="193" width="464" height="22" fill="url(#ghDeck)" rx="2"/>

            {{-- ======== HELIPAD (far left on deck) ======== --}}
            <rect x="75"  y="175" width="28"  height="20" fill="#111e5c"/>
            <circle cx="89"  cy="168" r="28"  fill="#111e5c" stroke="#253888" stroke-width="2"/>
            <circle cx="89"  cy="168" r="22"  fill="none"    stroke="#f5c842" stroke-width="1.5" opacity="0.65"/>
            <line x1="81"  y1="161" x2="81"  y2="175"       stroke="#f5c842" stroke-width="2.5" opacity="0.65"/>
            <line x1="81"  y1="168" x2="97"  y2="168"       stroke="#f5c842" stroke-width="2.5" opacity="0.65"/>
            <line x1="97"  y1="161" x2="97"  y2="175"       stroke="#f5c842" stroke-width="2.5" opacity="0.65"/>
            <circle cx="89"  cy="141" r="2.5" fill="#ff3030" filter="url(#ghGlow)"/>

            {{-- ======== LIVING QUARTERS (multi-story, left section) ======== --}}
            <rect x="130" y="92"  width="92"  height="100" fill="#0e1852" rx="2"/>
            <rect x="130" y="122" width="92"  height="2"   fill="#253888" opacity="0.42"/>
            <rect x="130" y="155" width="92"  height="2"   fill="#253888" opacity="0.42"/>
            {{-- Row 1 windows --}}
            <rect x="137" y="98"  width="11"  height="8"  fill="#f5c842" opacity="0.55" rx="1"/>
            <rect x="153" y="98"  width="11"  height="8"  fill="#f5c842" opacity="0.38" rx="1"/>
            <rect x="169" y="98"  width="11"  height="8"  fill="#f5c842" opacity="0.68" rx="1"/>
            <rect x="185" y="98"  width="11"  height="8"  fill="#f5c842" opacity="0.30" rx="1"/>
            <rect x="201" y="98"  width="11"  height="8"  fill="#f5c842" opacity="0.55" rx="1"/>
            {{-- Row 2 windows --}}
            <rect x="137" y="130" width="11"  height="8"  fill="#f5c842" opacity="0.45" rx="1"/>
            <rect x="153" y="130" width="11"  height="8"  fill="#f5c842" opacity="0.65" rx="1"/>
            <rect x="169" y="130" width="11"  height="8"  fill="#f5c842" opacity="0.35" rx="1"/>
            <rect x="185" y="130" width="11"  height="8"  fill="#f5c842" opacity="0.72" rx="1"/>
            <rect x="201" y="130" width="11"  height="8"  fill="#f5c842" opacity="0.42" rx="1"/>
            {{-- Row 3 windows --}}
            <rect x="137" y="162" width="11"  height="8"  fill="#f5c842" opacity="0.58" rx="1"/>
            <rect x="153" y="162" width="11"  height="8"  fill="#f5c842" opacity="0.40" rx="1"/>
            <rect x="169" y="162" width="11"  height="8"  fill="#f5c842" opacity="0.50" rx="1"/>
            <rect x="185" y="162" width="11"  height="8"  fill="#f5c842" opacity="0.35" rx="1"/>
            {{-- Roof --}}
            <rect x="128" y="90"  width="96"  height="4"  fill="#152268"/>
            <rect x="138" y="88"  width="3"   height="10" fill="#1a2870"/>
            <rect x="155" y="85"  width="3"   height="8"  fill="#1a2870"/>
            <rect x="180" y="86"  width="8"   height="5"  fill="#1a2870" rx="1"/>
            <rect x="210" y="87"  width="4"   height="7"  fill="#1a2870"/>

            {{-- ======== GAS/OIL SEPARATOR VESSELS ======== --}}
            {{-- Vessel 1 (main separator) --}}
            <ellipse cx="240" cy="170" rx="8"  ry="16" fill="#0a1240"/>
            <rect    x="240" y="154" width="64"  height="32" fill="#0e1852" rx="2"/>
            <ellipse cx="304" cy="170" rx="8"  ry="16" fill="#1a2870"/>
            <rect    x="247" y="163" width="50"  height="4"  fill="#152268" opacity="0.48"/>
            <rect    x="253" y="152" width="5"   height="8"  fill="#1a2870"/>
            <rect    x="272" y="152" width="5"   height="8"  fill="#1a2870"/>
            <rect    x="289" y="152" width="5"   height="8"  fill="#1a2870"/>
            <rect    x="266" y="184" width="5"   height="8"  fill="#1a2870"/>
            {{-- Vessel 2 (scrubber, smaller) --}}
            <ellipse cx="240" cy="137" rx="5"  ry="11" fill="#0a1240"/>
            <rect    x="240" y="126" width="44"  height="22" fill="#0e1852" rx="2"/>
            <ellipse cx="284" cy="137" rx="5"  ry="11" fill="#1a2870"/>
            <rect    x="242" y="129" width="4"   height="5"  fill="#253888"/>
            <rect    x="248" y="129" width="4"   height="5"  fill="#253888"/>
            {{-- Connecting pipe --}}
            <rect    x="259" y="148" width="4"   height="8"  fill="#1a2870"/>
            <rect    x="311" y="165" width="20"  height="4"  fill="#1a2870"/>
            <rect    x="327" y="161" width="4"   height="14" fill="#1a2870"/>

            {{-- ======== DRILLING DERRICK (triangular lattice tower) ======== --}}
            {{-- Main legs --}}
            <line x1="338" y1="212" x2="361" y2="40"  stroke="#152268" stroke-width="4.5" stroke-linecap="round"/>
            <line x1="392" y1="212" x2="372" y2="40"  stroke="#152268" stroke-width="4.5" stroke-linecap="round"/>
            {{-- Horizontal cross members --}}
            <line x1="340" y1="193" x2="390" y2="193" stroke="#152268" stroke-width="2.5"/>
            <line x1="342" y1="173" x2="388" y2="173" stroke="#152268" stroke-width="2.5"/>
            <line x1="344" y1="153" x2="386" y2="153" stroke="#152268" stroke-width="2.5"/>
            <line x1="347" y1="133" x2="383" y2="133" stroke="#152268" stroke-width="2.5"/>
            <line x1="350" y1="113" x2="380" y2="113" stroke="#152268" stroke-width="2.5"/>
            <line x1="353" y1="93"  x2="377" y2="93"  stroke="#152268" stroke-width="2.5"/>
            <line x1="355" y1="74"  x2="375" y2="74"  stroke="#152268" stroke-width="2.5"/>
            <line x1="357" y1="56"  x2="373" y2="56"  stroke="#152268" stroke-width="2.5"/>
            {{-- Diagonal bracing --}}
            <line x1="340" y1="193" x2="356" y2="173" stroke="#152268" stroke-width="1.5"/>
            <line x1="374" y1="173" x2="390" y2="193" stroke="#152268" stroke-width="1.5"/>
            <line x1="342" y1="173" x2="357" y2="153" stroke="#152268" stroke-width="1.5"/>
            <line x1="373" y1="153" x2="388" y2="173" stroke="#152268" stroke-width="1.5"/>
            <line x1="344" y1="153" x2="358" y2="133" stroke="#152268" stroke-width="1.5"/>
            <line x1="372" y1="133" x2="386" y2="153" stroke="#152268" stroke-width="1.5"/>
            <line x1="347" y1="133" x2="360" y2="113" stroke="#152268" stroke-width="1.5"/>
            <line x1="370" y1="113" x2="383" y2="133" stroke="#152268" stroke-width="1.5"/>
            <line x1="350" y1="113" x2="362" y2="93"  stroke="#152268" stroke-width="1.5"/>
            <line x1="368" y1="93"  x2="380" y2="113" stroke="#152268" stroke-width="1.5"/>
            <line x1="353" y1="93"  x2="363" y2="74"  stroke="#152268" stroke-width="1.5"/>
            <line x1="367" y1="74"  x2="377" y2="93"  stroke="#152268" stroke-width="1.5"/>
            <line x1="355" y1="74"  x2="363" y2="56"  stroke="#152268" stroke-width="1.5"/>
            <line x1="367" y1="56"  x2="375" y2="74"  stroke="#152268" stroke-width="1.5"/>
            {{-- Crown block --}}
            <rect x="358" y="36"  width="16"  height="7"  fill="#1a2870" rx="1"/>
            {{-- Draw cable --}}
            <line x1="366" y1="43"  x2="366" y2="120" stroke="#253888" stroke-width="1.5" opacity="0.65"/>
            <rect x="359" y="120" width="14"  height="5"  fill="#253888" opacity="0.65"/>
            {{-- Draw works --}}
            <rect x="346" y="202" width="28"  height="12" fill="#111e5c" rx="1"/>
            <rect x="334" y="210" width="62"  height="4"  fill="#152268"/>
            {{-- Aviation light --}}
            <circle cx="366" cy="33" r="3.5"  fill="#ff3030" filter="url(#ghGlow)"/>

            {{-- ======== CRANE ======== --}}
            <rect x="418" y="183" width="22"  height="22" fill="#0e1852" rx="2"/>
            <line x1="429" y1="189" x2="478" y2="118" stroke="#152268" stroke-width="3.5" stroke-linecap="round"/>
            <line x1="436" y1="193" x2="485" y2="122" stroke="#152268" stroke-width="3.5" stroke-linecap="round"/>
            <line x1="438" y1="173" x2="442" y2="161" stroke="#1a2870" stroke-width="2"/>
            <line x1="448" y1="158" x2="452" y2="146" stroke="#1a2870" stroke-width="2"/>
            <line x1="458" y1="143" x2="462" y2="131" stroke="#1a2870" stroke-width="2"/>
            <line x1="481" y1="120" x2="481" y2="162" stroke="#253888" stroke-width="1.5"/>
            <path d="M477,162 Q481,170 485,162" stroke="#253888" stroke-width="2" fill="none"/>
            <rect x="414" y="175" width="18"  height="10" fill="#0a1240" rx="2"/>

            {{-- ======== FLARE BOOM ======== --}}
            <line x1="492" y1="208" x2="551" y2="130" stroke="#0e1852" stroke-width="5.5" stroke-linecap="round"/>
            <line x1="492" y1="212" x2="551" y2="134" stroke="#1a2870" stroke-width="2"   opacity="0.38"/>
            <path d="M549,133 L553,129 L557,133 L555,136 L551,136 Z" fill="#1a2870"/>
            {{-- Flame --}}
            <ellipse cx="553" cy="126" rx="12"  ry="20" fill="url(#ghFlare)" opacity="0.55"/>
            <path d="M547,134 Q549,116 553,102 Q557,116 559,134 Z" fill="#ff7b00" opacity="0.9"/>
            <path d="M549,134 Q551,118 553,106 Q555,118 557,134 Z" fill="#ffaa20" opacity="0.85"/>
            <path d="M551,134 Q552,122 553,113 Q554,122 555,134 Z" fill="#ffdf60" opacity="0.8"/>
            <ellipse cx="553" cy="116" rx="26"  ry="32" fill="#ff6600" opacity="0.07"/>
            <circle  cx="553" cy="128" r="2"    fill="#ffaa00" filter="url(#ghGlow)"/>

            {{-- ======== DECK PIPE HEADER ======== --}}
            <rect x="58" y="178" width="464"  height="5" fill="#152268" opacity="0.65"/>
            <rect x="96"  y="180" width="4"   height="6" fill="#1a2870"/>
            <rect x="210" y="180" width="4"   height="6" fill="#1a2870"/>
            <rect x="380" y="180" width="4"   height="6" fill="#1a2870"/>
            <rect x="500" y="180" width="4"   height="6" fill="#1a2870"/>

            {{-- ======== NAVIGATION LIGHTS ======== --}}
            <circle cx="58"  cy="192" r="2.5" fill="#ff3030" filter="url(#ghGlow)"/>
            <circle cx="522" cy="192" r="2.5" fill="#22cc44" filter="url(#ghGlow)"/>
            <circle cx="290" cy="184" r="2"   fill="white"   filter="url(#ghGlow)" opacity="0.82"/>

            {{-- ======== MOORING LINES ======== --}}
            <line x1="78"  y1="368" x2="24"  y2="430" stroke="#1a2870" stroke-width="2" opacity="0.38"/>
            <line x1="502" y1="368" x2="556" y2="430" stroke="#1a2870" stroke-width="2" opacity="0.38"/>

            {{-- ======== UNDERWATER PIPELINE ======== --}}
            <path d="M0,510 Q60,502 120,510 Q180,518 240,510 Q300,502 360,510 Q420,518 480,510 Q530,504 580,510"
                  stroke="#1a2870" stroke-width="8" fill="none" opacity="0.52"/>
            <rect x="145" y="504" width="6"  height="14" fill="#1a2870" rx="1" opacity="0.55"/>
            <rect x="305" y="504" width="6"  height="14" fill="#1a2870" rx="1" opacity="0.55"/>
            <rect x="448" y="504" width="6"  height="14" fill="#1a2870" rx="1" opacity="0.55"/>

            {{-- Underwater haze --}}
            <rect y="400" width="580" height="160" fill="#030d28" opacity="0.42"/>
            {{-- Bottom fade --}}
            <rect y="455" width="580" height="105" fill="url(#ghBotFade)"/>

            {{-- Steam wisps from LQ roof --}}
            <path d="M140,86 Q144,70 139,58 Q134,44 140,32"
                  stroke="rgba(200,220,255,0.1)" stroke-width="3" fill="none" stroke-linecap="round"/>
            <path d="M158,84 Q162,70 157,59"
                  stroke="rgba(200,220,255,0.08)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
        </svg>

        {{-- Floating badges --}}
        <div class="ghero-badge ghero-badge-1">
            <i class="fas fa-shield-alt"></i>
            <span>Proveedores Verificados</span>
        </div>
        <div class="ghero-badge ghero-badge-2">
            <i class="fas fa-handshake"></i>
            <span>Servicio de Calidad</span>
        </div>

        {{-- Bottom label --}}
        <div class="ghero-left-label">
            <p>Plataforma integral para la gestión y monitoreo<br>de precios y proveedores.</p>
        </div>
    </div>
    {{-- /.ghero-left --}}

    {{-- ======== RIGHT PANEL — Welcome content ======== --}}
    <div class="ghero-right">

        {{-- Welcome pill --}}
        <div class="ghero-welcome-pill">
            <i class="fas fa-star"></i>
            <span>Bienvenido a {{ config('settings.site_name') }}</span>
        </div>

        {{-- Title --}}
        @if(@$hero->title)
            <h1 class="ghero-title">{!! $hero->title !!}</h1>
        @else
            <h1 class="ghero-title">Gestión de<br><b>Hidrocarburos</b></h1>
        @endif

        {{-- Subtitle --}}
        @if(@$hero->sub_title)
            <div class="ghero-subtitle">{!! $hero->sub_title !!}</div>
        @endif

        {{-- CTA --}}
        <div class="ghero-cta">
            <a href="{{ route('login') }}" class="ghero-btn">
                <i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión
            </a>
        </div>

        {{-- Features --}}
        <div class="ghero-features">
            <div class="ghero-feature">
                <i class="fas fa-check-circle"></i>
                <span>Acceso a proveedores exclusivos</span>
            </div>
            <div class="ghero-feature">
                <i class="fas fa-check-circle"></i>
                <span>Precios de combustibles actualizados en día con día</span>
            </div>
        </div>

    </div>
    {{-- /.ghero-right --}}

</div>
@endsection
