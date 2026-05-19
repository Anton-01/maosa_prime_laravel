<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <title>{{ config('settings.site_name') }}</title>
    <link rel="icon" type="image/png" href="{{ config('settings.favicon') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/maosa/auth.css') }}">
    <style>
        :root { --colorPrimary: {{ config('settings.site_default_color') }}; }
    </style>
    @stack('auth-styles')
</head>
<body class="auth-body">

<div class="auth-container">

    {{-- ===================== LEFT PANEL — Hydrocarbon Plant Illustration ===================== --}}
    <div class="auth-panel-left">

        {{-- SVG: Night refinery / hydrocarbon processing plant scene --}}
        <svg xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 580 620"
             preserveAspectRatio="xMidYMid slice"
             class="auth-refinery-svg"
             aria-hidden="true">
            <defs>
                <linearGradient id="authSkyGr" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#030921"/>
                    <stop offset="55%"  stop-color="#0b1440"/>
                    <stop offset="100%" stop-color="#141e5c"/>
                </linearGradient>
                <linearGradient id="authGroundGr" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#0c1540"/>
                    <stop offset="100%" stop-color="#060e28"/>
                </linearGradient>
                <linearGradient id="authCol1Gr" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%"   stop-color="#111e5c"/>
                    <stop offset="45%"  stop-color="#1c2e7a"/>
                    <stop offset="100%" stop-color="#111e5c"/>
                </linearGradient>
                <linearGradient id="authCol2Gr" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%"   stop-color="#0e1a54"/>
                    <stop offset="50%"  stop-color="#182872"/>
                    <stop offset="100%" stop-color="#0e1a54"/>
                </linearGradient>
                <linearGradient id="authFadeBot" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="#060e28" stop-opacity="0"/>
                    <stop offset="100%" stop-color="#030921" stop-opacity="0.85"/>
                </linearGradient>
                <radialGradient id="authMoonGr" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="#fce96a"/>
                    <stop offset="65%"  stop-color="#f5ca18"/>
                    <stop offset="100%" stop-color="#e6b000"/>
                </radialGradient>
                <radialGradient id="authMoonGlow" cx="50%" cy="50%" r="50%">
                    <stop offset="0%"   stop-color="#f5c842" stop-opacity="0.38"/>
                    <stop offset="100%" stop-color="#f5c842" stop-opacity="0"/>
                </radialGradient>
                <radialGradient id="authFlareGr" cx="50%" cy="0%" r="100%">
                    <stop offset="0%"   stop-color="#ff8c00" stop-opacity="1"/>
                    <stop offset="55%"  stop-color="#ff5000" stop-opacity="0.6"/>
                    <stop offset="100%" stop-color="#ff3300" stop-opacity="0"/>
                </radialGradient>
                <filter id="authGlow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="2.5" result="blur"/>
                    <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
                <filter id="authSoftGlow" x="-60%" y="-60%" width="220%" height="220%">
                    <feGaussianBlur stdDeviation="5" result="blur"/>
                    <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
            </defs>

            {{-- Sky --}}
            <rect width="580" height="620" fill="url(#authSkyGr)"/>

            {{-- Stars --}}
            <g opacity="0.88">
                <circle cx="22"  cy="16"  r="1"   fill="white"/>
                <circle cx="58"  cy="38"  r="1.4" fill="white"/>
                <circle cx="90"  cy="10"  r="1"   fill="white"/>
                <circle cx="118" cy="48"  r="1"   fill="white"/>
                <circle cx="150" cy="20"  r="1.2" fill="white"/>
                <circle cx="178" cy="62"  r="1"   fill="white"/>
                <circle cx="212" cy="14"  r="1.4" fill="white"/>
                <circle cx="248" cy="36"  r="1"   fill="white"/>
                <circle cx="282" cy="7"   r="1"   fill="white"/>
                <circle cx="318" cy="53"  r="1.2" fill="white"/>
                <circle cx="352" cy="19"  r="1"   fill="white"/>
                <circle cx="376" cy="40"  r="1"   fill="white"/>
                <circle cx="32"  cy="72"  r="1"   fill="white"/>
                <circle cx="68"  cy="92"  r="1"   fill="white"/>
                <circle cx="102" cy="68"  r="1.2" fill="white"/>
                <circle cx="138" cy="108" r="1"   fill="white"/>
                <circle cx="168" cy="84"  r="1"   fill="white"/>
                <circle cx="196" cy="118" r="1"   fill="white"/>
                <circle cx="228" cy="92"  r="1.2" fill="white"/>
                <circle cx="260" cy="130" r="1"   fill="white"/>
                <circle cx="296" cy="74"  r="1"   fill="white"/>
                <circle cx="333" cy="112" r="1"   fill="white"/>
                <circle cx="52"  cy="148" r="1"   fill="white"/>
                <circle cx="82"  cy="138" r="1.2" fill="white"/>
                <circle cx="112" cy="162" r="1"   fill="white"/>
                <circle cx="18"  cy="34"  r="0.8" fill="white" opacity="0.65"/>
                <circle cx="44"  cy="54"  r="0.8" fill="white" opacity="0.55"/>
                <circle cx="74"  cy="27"  r="0.8" fill="white" opacity="0.75"/>
                <circle cx="100" cy="83"  r="0.8" fill="white" opacity="0.55"/>
                <circle cx="132" cy="28"  r="0.8" fill="white" opacity="0.7"/>
                <circle cx="160" cy="98"  r="0.8" fill="white" opacity="0.5"/>
                <circle cx="187" cy="44"  r="0.8" fill="white" opacity="0.85"/>
                <circle cx="220" cy="70"  r="0.8" fill="white" opacity="0.6"/>
                <circle cx="252" cy="103" r="0.8" fill="white" opacity="0.7"/>
                <circle cx="278" cy="54"  r="0.8" fill="white" opacity="0.8"/>
                <circle cx="307" cy="30"  r="0.8" fill="white" opacity="0.6"/>
                <circle cx="342" cy="78"  r="0.8" fill="white" opacity="0.7"/>
                <circle cx="368" cy="56"  r="0.8" fill="white" opacity="0.5"/>
                <circle cx="395" cy="32"  r="0.8" fill="white" opacity="0.85"/>
            </g>

            {{-- Shooting stars --}}
            <g opacity="0.75">
                <line x1="118" y1="24" x2="158" y2="50" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="116" y1="24" x2="120" y2="27" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="222" y1="17" x2="250" y2="36" stroke="white" stroke-width="1"   stroke-linecap="round"/>
                <line x1="348" y1="33" x2="370" y2="48" stroke="white" stroke-width="1"   stroke-linecap="round"/>
            </g>

            {{-- Moon glow halo --}}
            <circle cx="482" cy="88" r="66" fill="url(#authMoonGlow)"/>
            {{-- Moon --}}
            <circle cx="482" cy="88" r="38" fill="url(#authMoonGr)" filter="url(#authSoftGlow)"/>
            {{-- Moon surface detail --}}
            <circle cx="470" cy="77" r="7"   fill="#daa800" opacity="0.3"/>
            <circle cx="492" cy="97" r="5"   fill="#daa800" opacity="0.25"/>
            <circle cx="480" cy="103" r="3.5" fill="#daa800" opacity="0.2"/>

            {{-- Atmospheric haze bands --}}
            <ellipse cx="105" cy="178" rx="115" ry="38" fill="#0d1848" opacity="0.45"/>
            <ellipse cx="355" cy="152" rx="95"  ry="32" fill="#0d1848" opacity="0.38"/>

            {{-- Background hills — far --}}
            <path d="M0,318 Q85,268 205,294 Q325,316 445,266 Q522,238 580,270 L580,480 L0,480 Z"
                  fill="#0e1a50"/>

            {{-- Background hills — mid --}}
            <path d="M0,358 Q105,328 225,348 Q335,366 455,328 Q522,310 580,338 L580,480 L0,480 Z"
                  fill="#111f5c"/>

            {{-- Ground base --}}
            <rect y="442" width="580" height="178" fill="url(#authGroundGr)"/>
            <rect y="442" width="580" height="3" fill="#1c2a6e"/>
            <rect y="445" width="580" height="4" fill="#0e1840"/>

            {{-- ============ STORAGE TANKS (left cluster) ============ --}}
            {{-- Large tank --}}
            <ellipse cx="70"  cy="400" rx="52" ry="13" fill="#0d1848"/>
            <rect x="18"  y="363" width="104" height="38" fill="#111e5c"/>
            <ellipse cx="70"  cy="363" rx="52" ry="13" fill="#1a2870"/>
            <polygon points="18,363 70,347 122,363" fill="#152268"/>
            <path d="M25,378 Q70,373 115,378" stroke="#233080" stroke-width="1" fill="none" opacity="0.45"/>
            {{-- Nozzles --}}
            <rect x="122" y="374" width="18" height="4" fill="#1a2870"/>
            <rect x="-18" y="374" width="18" height="4" fill="#1a2870" transform="translate(18,0)"/>

            {{-- Medium tank --}}
            <ellipse cx="187" cy="414" rx="36" ry="9"  fill="#0d1848"/>
            <rect x="151" y="382" width="72"  height="33" fill="#111e5c"/>
            <ellipse cx="187" cy="382" rx="36" ry="9"  fill="#1a2870"/>
            <polygon points="151,382 187,370 223,382" fill="#152268"/>

            {{-- Small tank --}}
            <ellipse cx="248" cy="422" rx="23" ry="6"  fill="#0d1848"/>
            <rect x="225" y="400" width="46"  height="22" fill="#111e5c"/>
            <ellipse cx="248" cy="400" rx="23" ry="6"  fill="#1a2870"/>
            <polygon points="225,400 248,391 271,400" fill="#152268"/>

            {{-- ============ PIPE RACK ============ --}}
            <rect x="272" y="412" width="4" height="32" fill="#1a2868"/>
            <rect x="292" y="412" width="4" height="32" fill="#1a2868"/>
            <rect x="270" y="410" width="28" height="5" fill="#1e2e72"/>
            <rect x="270" y="420" width="28" height="4" fill="#1e2e72"/>
            <rect x="270" y="430" width="28" height="4" fill="#1e2e72"/>

            {{-- ============ MAIN DISTILLATION COLUMN (center, tall) ============ --}}
            {{-- Foundation / skirt --}}
            <polygon points="298,450 352,450 356,442 294,442" fill="#0d1848"/>
            <rect x="305" y="422" width="48"  height="28" fill="#0f185a"/>
            <rect x="314" y="428" width="10"  height="14" fill="#09112a"/>
            <rect x="335" y="428" width="10"  height="14" fill="#09112a"/>
            {{-- Column body --}}
            <rect x="300" y="178" width="58"  height="248" fill="url(#authCol1Gr)" rx="4"/>
            {{-- Top cap --}}
            <ellipse cx="329" cy="178" rx="32" ry="8"  fill="#1d2d7c"/>
            {{-- Overhead vapor outlet --}}
            <rect x="325" y="156" width="8"   height="26" fill="#1a2870"/>
            <rect x="318" y="153" width="22"  height="6"  fill="#1e3080"/>
            {{-- Tray section bands --}}
            <rect x="298" y="208" width="62"  height="3" fill="#253888" opacity="0.75"/>
            <rect x="298" y="234" width="62"  height="3" fill="#253888" opacity="0.75"/>
            <rect x="298" y="262" width="62"  height="3" fill="#253888" opacity="0.75"/>
            <rect x="298" y="290" width="62"  height="3" fill="#253888" opacity="0.75"/>
            <rect x="298" y="318" width="62"  height="3" fill="#253888" opacity="0.75"/>
            <rect x="298" y="346" width="62"  height="3" fill="#253888" opacity="0.75"/>
            <rect x="298" y="374" width="62"  height="3" fill="#253888" opacity="0.75"/>
            <rect x="298" y="402" width="62"  height="3" fill="#253888" opacity="0.75"/>
            {{-- Side nozzles --}}
            <rect x="358" y="224" width="22"  height="4" fill="#1a2870"/>
            <rect x="358" y="260" width="18"  height="4" fill="#1a2870"/>
            <rect x="358" y="306" width="22"  height="4" fill="#1a2870"/>
            <rect x="358" y="350" width="18"  height="4" fill="#1a2870"/>
            <rect x="278" y="294" width="22"  height="4" fill="#1a2870"/>
            <rect x="278" y="340" width="22"  height="4" fill="#1a2870"/>
            {{-- Ladder --}}
            <rect x="296" y="190" width="2"   height="248" fill="#253888" opacity="0.42"/>
            <rect x="294" y="205" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="220" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="235" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="250" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="265" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="280" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="295" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="310" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="325" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="340" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="355" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="370" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="385" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="400" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="415" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            <rect x="294" y="430" width="6"   height="1"   fill="#253888" opacity="0.42"/>
            {{-- Safety indicator light --}}
            <circle cx="329" cy="168" r="3"   fill="#ff3030" filter="url(#authGlow)"/>

            {{-- ============ CONDENSER (overhead) ============ --}}
            <rect x="398" y="218" width="30"  height="82" fill="#111e5c" rx="3"/>
            <ellipse cx="413" cy="218" rx="17" ry="5"  fill="#1a2870"/>
            <ellipse cx="413" cy="300" rx="17" ry="5"  fill="#0d1848"/>
            {{-- Overhead pipe from column top to condenser --}}
            <path d="M329,153 Q329,145 380,145 Q398,145 398,218"
                  stroke="#1a2870" stroke-width="5" fill="none"/>

            {{-- ============ HEAT EXCHANGER (shell & tube) ============ --}}
            <ellipse cx="374" cy="314" rx="8"  ry="14" fill="#0d1848"/>
            <rect x="374" y="300" width="74"  height="28" fill="#111e5c" rx="3"/>
            <ellipse cx="448" cy="314" rx="8"  ry="14" fill="#1a2870"/>
            <rect x="374" y="311" width="74"  height="6"  fill="#152268" opacity="0.4"/>
            {{-- HE outlet pipe --}}
            <rect x="448" y="310" width="14"  height="4" fill="#1a2870"/>
            <rect x="462" y="308" width="4"   height="22" fill="#1a2870"/>
            {{-- Condensate pot --}}
            <rect x="442" y="328" width="14"  height="24" fill="#0d1848" rx="2"/>
            <ellipse cx="449" cy="328" rx="7"  ry="4"  fill="#1a2870"/>

            {{-- Reboiler (horizontal, below column) --}}
            <ellipse cx="363" cy="392" rx="7"  ry="11" fill="#0d1848"/>
            <rect x="363" y="381" width="60"  height="22" fill="#111e5c" rx="3"/>
            <ellipse cx="423" cy="392" rx="7"  ry="11" fill="#1a2870"/>
            {{-- Reboiler pipe to column --}}
            <path d="M358,392 L350,392 Q348,392 348,385 L348,418 Q348,425 357,425"
                  stroke="#1a2870" stroke-width="4" fill="none"/>

            {{-- ============ SECONDARY DISTILLATION COLUMN (right) ============ --}}
            <rect x="468" y="440" width="38"  height="10" fill="#0d1848" rx="2"/>
            <rect x="470" y="284" width="34"  height="158" fill="url(#authCol2Gr)" rx="3"/>
            <ellipse cx="487" cy="284" rx="20" ry="6"  fill="#1a2870"/>
            {{-- Top vapor line --}}
            <rect x="483" y="267" width="6"   height="20" fill="#1a2870"/>
            <rect x="476" y="264" width="20"  height="5"  fill="#1a2870"/>
            {{-- Bands --}}
            <rect x="468" y="305" width="38"  height="2" fill="#253888" opacity="0.6"/>
            <rect x="468" y="326" width="38"  height="2" fill="#253888" opacity="0.6"/>
            <rect x="468" y="347" width="38"  height="2" fill="#253888" opacity="0.6"/>
            <rect x="468" y="368" width="38"  height="2" fill="#253888" opacity="0.6"/>
            <rect x="468" y="389" width="38"  height="2" fill="#253888" opacity="0.6"/>
            <rect x="468" y="410" width="38"  height="2" fill="#253888" opacity="0.6"/>
            {{-- Nozzles --}}
            <rect x="504" y="310" width="14"  height="3" fill="#1a2870"/>
            <rect x="504" y="342" width="14"  height="3" fill="#1a2870"/>
            <rect x="452" y="362" width="16"  height="3" fill="#1a2870"/>
            {{-- Indicator --}}
            <circle cx="487" cy="276" r="2.5"  fill="#ff3030" filter="url(#authGlow)"/>

            {{-- ============ FLARE STACK (far right) ============ --}}
            <rect x="549" y="188" width="8"   height="258" fill="#0f1848"/>
            <rect x="545" y="188" width="2"   height="258" fill="#1a2870" opacity="0.45"/>
            {{-- Platforms --}}
            <rect x="541" y="248" width="22"  height="3" fill="#152060"/>
            <rect x="541" y="310" width="22"  height="3" fill="#152060"/>
            <rect x="541" y="372" width="22"  height="3" fill="#152060"/>
            {{-- Tip --}}
            <path d="M546,193 L553,188 L560,193 L557,197 L549,197 Z" fill="#1a2870"/>
            {{-- Flame --}}
            <ellipse cx="553" cy="190" rx="14" ry="22" fill="url(#authFlareGr)" opacity="0.55"/>
            <path d="M546,200 Q548,180 553,165 Q558,180 560,200 Z" fill="#ff7b00" opacity="0.9"/>
            <path d="M548,200 Q550,185 553,173 Q556,185 558,200 Z" fill="#ffaa20" opacity="0.85"/>
            <path d="M550,200 Q551,190 553,182 Q555,190 556,200 Z" fill="#ffdf60" opacity="0.8"/>
            <ellipse cx="553" cy="178" rx="28" ry="32" fill="#ff6600" opacity="0.07"/>
            {{-- Indicator --}}
            <circle cx="549" cy="188" r="2"   fill="#ffaa00" filter="url(#authGlow)"/>

            {{-- ============ CONTROL ROOM ============ --}}
            <rect x="148" y="392" width="90"  height="54" fill="#0e1850" rx="2"/>
            <polygon points="145,392 193,380 240,392" fill="#111e5c"/>
            {{-- Lit windows --}}
            <rect x="156" y="400" width="13"  height="9"  fill="#f5c842" opacity="0.55" rx="1"/>
            <rect x="175" y="400" width="13"  height="9"  fill="#f5c842" opacity="0.42" rx="1"/>
            <rect x="194" y="400" width="13"  height="9"  fill="#f5c842" opacity="0.68" rx="1"/>
            <rect x="213" y="400" width="13"  height="9"  fill="#f5c842" opacity="0.33" rx="1"/>
            <rect x="156" y="416" width="13"  height="8"  fill="#f5c842" opacity="0.38" rx="1"/>
            <rect x="175" y="416" width="13"  height="8"  fill="#f5c842" opacity="0.52" rx="1"/>
            <rect x="194" y="416" width="13"  height="8"  fill="#f5c842" opacity="0.28" rx="1"/>
            {{-- Door --}}
            <rect x="178" y="426" width="14"  height="20" fill="#09112a" rx="1"/>
            {{-- Antenna --}}
            <rect x="191" y="372" width="2"   height="12" fill="#253888"/>
            <rect x="188" y="372" width="8"   height="1"  fill="#253888"/>

            {{-- ============ COMPRESSOR BUILDING (right cluster) ============ --}}
            <rect x="438" y="402" width="60"  height="42" fill="#0e1850" rx="2"/>
            <polygon points="435,402 468,390 500,402" fill="#111e5c"/>
            <rect x="445" y="410" width="10"  height="8"  fill="#f5c230" opacity="0.52" rx="1"/>
            <rect x="461" y="410" width="10"  height="8"  fill="#f5c230" opacity="0.44" rx="1"/>
            <rect x="477" y="410" width="10"  height="8"  fill="#f5c230" opacity="0.58" rx="1"/>
            <rect x="457" y="424" width="13"  height="20" fill="#09112a" rx="1"/>

            {{-- ============ PUMP STATION ============ --}}
            <rect x="118" y="422" width="26"  height="18" fill="#0e1850" rx="2"/>
            <ellipse cx="131" cy="422" rx="10" ry="4"  fill="#152268"/>
            <rect x="118" y="418" width="26"  height="5"  fill="#1a2868"/>

            {{-- ============ MAIN PIPE HEADER ============ --}}
            <rect x="0"   y="445" width="580" height="6" fill="#152268"/>
            <rect x="0"   y="451" width="580" height="3" fill="#1a2870" opacity="0.38"/>

            {{-- Vertical drops to header --}}
            <rect x="68"  y="413" width="5"   height="32" fill="#152268"/>
            <rect x="185" y="423" width="5"   height="22" fill="#152268"/>
            <rect x="246" y="428" width="5"   height="17" fill="#152268"/>
            <rect x="327" y="426" width="5"   height="19" fill="#152268"/>
            <rect x="485" y="444" width="5"   height="6"  fill="#152268"/>

            {{-- Overhead pipe: column to condenser --}}
            <path d="M380,228 Q385,224 398,224"
                  stroke="#1a2870" stroke-width="4" fill="none"/>
            <path d="M380,263 Q385,260 398,260"
                  stroke="#1a2870" stroke-width="4" fill="none"/>
            <path d="M380,308 Q385,305 398,305"
                  stroke="#1a2870" stroke-width="4" fill="none"/>
            <path d="M380,350 Q385,347 398,347"
                  stroke="#1a2870" stroke-width="4" fill="none"/>

            {{-- Column reflux return --}}
            <path d="M413,300 Q413,312 400,312 Q388,312 378,304 Q368,294 362,305"
                  stroke="#1a2870" stroke-width="4" fill="none"/>

            {{-- HE to secondary column pipeline --}}
            <path d="M466,392 L460,392 Q456,392 456,386 L456,340 Q456,332 468,332"
                  stroke="#1a2870" stroke-width="4" fill="none"/>

            {{-- Steam wisps from column tops --}}
            <path d="M329,153 Q334,137 328,124 Q323,110 330,98"
                  stroke="rgba(200,220,255,0.12)" stroke-width="4" fill="none" stroke-linecap="round"/>
            <path d="M322,153 Q317,140 322,130"
                  stroke="rgba(200,220,255,0.08)" stroke-width="3" fill="none" stroke-linecap="round"/>
            <path d="M487,264 Q492,250 486,238"
                  stroke="rgba(200,220,255,0.08)" stroke-width="3" fill="none" stroke-linecap="round"/>

            {{-- Ground reflection (subtle) --}}
            <rect x="303" y="452" width="52"  height="28" fill="#1d2d7a" opacity="0.12" rx="2"/>
            <rect x="472" y="454" width="32"  height="18" fill="#1d2d7a" opacity="0.1"  rx="2"/>

            {{-- Bottom fade overlay --}}
            <rect y="418" width="580" height="202" fill="url(#authFadeBot)"/>

            {{-- Wave decoration at bottom --}}
            <path d="M0,555 Q72,538 145,550 Q218,562 290,545 Q362,528 435,542 Q507,556 580,540 L580,620 L0,620 Z"
                  fill="#1a2870" opacity="0.55"/>
            <path d="M0,575 Q90,558 180,570 Q270,582 360,565 Q450,548 540,562 Q560,566 580,560 L580,620 L0,620 Z"
                  fill="#1a2870" opacity="0.4"/>
        </svg>

        {{-- Left panel text content --}}
        <div class="auth-left-content">
            @if(config('settings.logo'))
                <a href="{{ route('start') }}" class="auth-left-logo">
                    <img src="{{ config('settings.logo') }}" alt="{{ config('settings.site_name') }}">
                </a>
            @endif
            <h3>Procesamiento de Hidrocarburos</h3>
            <p>Plataforma integral para la gestión y monitoreo de plantas de tratamiento de hidrocarburos.</p>
        </div>
    </div>
    {{-- /.auth-panel-left --}}

    {{-- ===================== RIGHT PANEL — Form ===================== --}}
    <div class="auth-panel-right">
        <a href="{{ route('start') }}" class="auth-corner-link">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Inicio
        </a>

        @yield('auth-content')
    </div>
    {{-- /.auth-panel-right --}}

</div>
{{-- /.auth-container --}}

<script src="{{ asset('frontend/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>
@stack('auth-scripts')
</body>
</html>
