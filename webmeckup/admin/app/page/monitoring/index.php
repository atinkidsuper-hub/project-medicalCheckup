<?php
$pageTitle = 'Monitoring Alat';
require_once '../../../../include/header.php';
?>
<style>
    :root {
        --bg-mon: #f0f2f8;
        --card-mon: #ffffff;
        --card-border: #e2e6ef;
        --accent-blue: #3B5BDB;
        --accent-cyan: #1971C2;
        --accent-green: #2F9E44;
        --accent-red: #E03131;
        --accent-yellow: #E67700;
        --accent-purple: #7048E8;
        --accent-orange: #E8590C;
        --text-primary: #1A1B2E;
        --text-secondary: #5A6072;
        --text-muted: #A0A5B5;
        --shadow-card: 0 4px 20px rgba(0, 0, 0, .06);
        --shadow-hover: 0 8px 28px rgba(0, 0, 0, .10);
    }

    .mon-dash {
        max-width: 1280px;
        margin: 0 auto;
    }

    /* Header */
    .mon-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 28px;
    }

    .mon-header-left h1 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mon-header-left h1 i {
        color: var(--accent-blue);
        font-size: 1.4rem;
    }

    .mon-header-left p {
        color: var(--text-secondary);
        font-size: .85rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .mon-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Status Badge */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px 6px 12px;
        border-radius: 50px;
        font-size: .82rem;
        font-weight: 700;
        letter-spacing: .3px;
        border: 1px solid var(--card-border);
    }

    .status-pill .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-pill.online {
        background: #EBFBEE;
        color: var(--accent-green);
        border-color: #B2F2BB;
    }

    .status-pill.online .dot {
        background: var(--accent-green);
        box-shadow: 0 0 8px var(--accent-green);
        animation: pulseDot 1.2s ease-in-out infinite;
    }

    .status-pill.offline {
        background: #FFF5F5;
        color: var(--accent-red);
        border-color: #FFC9C9;
    }

    .status-pill.offline .dot {
        background: var(--accent-red);
    }

    .status-pill.scanning {
        background: #FFF9DB;
        color: var(--accent-yellow);
        border-color: #FFE8A0;
    }

    .status-pill.scanning .dot {
        background: var(--accent-yellow);
        box-shadow: 0 0 8px var(--accent-yellow);
        animation: pulseDot .6s ease-in-out infinite;
    }

    .status-pill.measuring {
        background: #E7F5FF;
        color: var(--accent-cyan);
        border-color: #A5D8FF;
    }

    .status-pill.measuring .dot {
        background: var(--accent-cyan);
        box-shadow: 0 0 8px var(--accent-cyan);
        animation: pulseDot .8s ease-in-out infinite;
    }

    .status-pill.error {
        background: #FFF5F5;
        color: var(--accent-red);
        border-color: #FFC9C9;
    }

    .status-pill.error .dot {
        background: var(--accent-red);
        box-shadow: 0 0 8px var(--accent-red);
        animation: pulseDot .5s ease-in-out infinite;
    }

    .status-pill.completed {
        background: #F3E8FF;
        color: var(--accent-purple);
        border-color: #D0BFFF;
    }

    .status-pill.completed .dot {
        background: var(--accent-purple);
        box-shadow: 0 0 8px var(--accent-purple);
    }

    @keyframes pulseDot {

        0%,
        100% {
            opacity: .4;
            transform: scale(.8);
        }

        50% {
            opacity: 1;
            transform: scale(1.1);
        }
    }

    /* Last Update */
    .last-update {
        font-size: .78rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .last-update i {
        font-size: .7rem;
    }

    /* Main Status Card */
    .main-status {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 16px;
        padding: 36px 40px;
        box-shadow: var(--shadow-card);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .main-status::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 91, 219, .04) 0%, transparent 70%);
        pointer-events: none;
    }

    .main-status-inner {
        display: flex;
        align-items: center;
        gap: 32px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .status-ring {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        position: relative;
    }

    .status-ring-inner {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 3px solid var(--card-border);
        font-size: 2rem;
        transition: all .5s;
    }

    .status-ring-inner.online {
        border-color: var(--accent-green);
        color: var(--accent-green);
        box-shadow: 0 0 30px rgba(47, 158, 68, .15);
    }

    .status-ring-inner.offline {
        border-color: var(--accent-red);
        color: var(--accent-red);
    }

    .status-ring-inner.scanning {
        border-color: var(--accent-yellow);
        color: var(--accent-yellow);
        box-shadow: 0 0 30px rgba(230, 119, 0, .15);
        animation: ringSpin 2s linear infinite;
    }

    .status-ring-inner.measuring {
        border-color: var(--accent-cyan);
        color: var(--accent-cyan);
        box-shadow: 0 0 30px rgba(25, 113, 194, .15);
        animation: ringPulse 1.5s ease-in-out infinite;
    }

    .status-ring-inner.error {
        border-color: var(--accent-red);
        color: var(--accent-red);
        box-shadow: 0 0 30px rgba(224, 49, 49, .25);
        animation: ringPulse .6s ease-in-out infinite;
    }

    .status-ring-inner.completed {
        border-color: var(--accent-purple);
        color: var(--accent-purple);
        box-shadow: 0 0 30px rgba(112, 72, 232, .15);
    }

    @keyframes ringSpin {
        0% {
            box-shadow: 0 0 30px rgba(230, 119, 0, .15);
        }

        50% {
            box-shadow: 0 0 50px rgba(230, 119, 0, .25);
        }

        100% {
            box-shadow: 0 0 30px rgba(230, 119, 0, .15);
        }
    }

    @keyframes ringPulse {

        0%,
        100% {
            box-shadow: 0 0 30px rgba(25, 113, 194, .15);
        }

        50% {
            box-shadow: 0 0 50px rgba(25, 113, 194, .3);
        }
    }

    .status-text h2 {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0 0 4px;
    }

    .status-text p {
        color: var(--text-secondary);
        font-size: .9rem;
        margin: 0;
    }

    .status-text .pesan {
        color: var(--accent-cyan);
        font-weight: 600;
        font-size: .85rem;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .info-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: var(--shadow-card);
        transition: all .3s;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .info-card .label {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--text-muted);
        font-weight: 700;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .info-card .value {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .info-card .value .uid-text {
        font-family: 'JetBrains Mono', monospace;
        color: var(--accent-yellow);
        font-size: .85rem;
    }

    .info-card .value .nama-text {
        color: var(--text-primary);
    }

    .info-card .value .empty-val {
        color: var(--text-muted);
        font-weight: 400;
    }

    /* Timeline */
    .timeline-section {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: var(--shadow-card);
        margin-bottom: 24px;
    }

    .timeline-title {
        font-size: .85rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .timeline-title i {
        color: var(--accent-blue);
    }

    .timeline {
        position: relative;
        padding-left: 36px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 13px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: var(--card-border);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 24px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: -36px;
        top: 2px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .7rem;
        z-index: 1;
        transition: all .4s;
    }

    .timeline-dot.pending {
        border-color: var(--card-border);
        color: var(--text-muted);
    }

    .timeline-dot.active {
        border-color: var(--accent-cyan);
        color: var(--accent-cyan);
        box-shadow: 0 0 16px rgba(25, 113, 194, .2);
        animation: dotPulse 1.2s ease-in-out infinite;
    }

    .timeline-dot.done {
        border-color: var(--accent-green);
        color: var(--accent-green);
        background: #EBFBEE;
    }

    .timeline-dot.error {
        border-color: var(--accent-red);
        color: var(--accent-red);
        background: #FFF5F5;
    }

    @keyframes dotPulse {

        0%,
        100% {
            box-shadow: 0 0 16px rgba(25, 113, 194, .2);
        }

        50% {
            box-shadow: 0 0 28px rgba(25, 113, 194, .4);
        }
    }

    .timeline-content h4 {
        font-size: .88rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 2px;
        transition: color .3s;
    }

    .timeline-content p {
        font-size: .8rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .timeline-item.pending .timeline-content h4 {
        color: var(--text-muted);
    }

    .timeline-item.active .timeline-content h4 {
        color: var(--accent-cyan);
    }

    .timeline-item.done .timeline-content h4 {
        color: var(--accent-green);
    }

    .timeline-item .tl-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 50px;
        font-size: .7rem;
        font-weight: 700;
        margin-top: 4px;
    }

    .timeline-item .tl-badge.on {
        background: #EBFBEE;
        color: var(--accent-green);
        border: 1px solid #B2F2BB;
    }

    .timeline-item .tl-badge.wait {
        background: #FFF9DB;
        color: var(--accent-yellow);
        border: 1px solid #FFE8A0;
    }

    .timeline-item .tl-badge.now {
        background: #E7F5FF;
        color: var(--accent-cyan);
        border: 1px solid #A5D8FF;
        animation: badgePulse 1.2s ease-in-out infinite;
    }

    @keyframes badgePulse {

        0%,
        100% {
            opacity: .7;
        }

        50% {
            opacity: 1;
        }
    }

    .timeline-item .tl-value {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--accent-cyan);
        margin-top: 4px;
    }

    /* Sensor Grid */
    .sensor-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .sensor-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        box-shadow: var(--shadow-card);
        transition: all .4s;
        position: relative;
        overflow: hidden;
    }

    .sensor-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        opacity: 0;
        transition: opacity .4s;
    }

    .sensor-card.has-value::before {
        opacity: 1;
    }

    .sensor-card.has-value:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
    }

    .sensor-card .s-icon {
        font-size: 1.8rem;
        margin-bottom: 8px;
        display: block;
    }

    .sensor-card .s-label {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--text-muted);
        font-weight: 700;
        margin-bottom: 6px;
    }

    .sensor-card .s-value {
        font-size: 1.6rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', monospace;
        color: var(--text-muted);
        transition: all .4s;
        min-height: 2rem;
    }

    .sensor-card .s-value.glow {
        color: var(--text-primary);
    }

    .sensor-card .s-status {
        font-size: .72rem;
        font-weight: 600;
        margin-top: 6px;
        color: var(--text-muted);
        transition: all .3s;
    }

    .sensor-card.suhu {
        --card-accent: var(--accent-red);
    }

    .sensor-card.suhu::before {
        background: var(--accent-red);
    }

    .sensor-card.suhu .s-icon {
        color: var(--accent-red);
    }

    .sensor-card.suhu .s-value.glow {
        color: var(--accent-red);
    }

    .sensor-card.spo2 {
        --card-accent: var(--accent-cyan);
    }

    .sensor-card.spo2::before {
        background: var(--accent-cyan);
    }

    .sensor-card.spo2 .s-icon {
        color: var(--accent-cyan);
    }

    .sensor-card.spo2 .s-value.glow {
        color: var(--accent-cyan);
    }

    .sensor-card.hr {
        --card-accent: var(--accent-purple);
    }

    .sensor-card.hr::before {
        background: var(--accent-purple);
    }

    .sensor-card.hr .s-icon {
        color: var(--accent-purple);
    }

    .sensor-card.hr .s-value.glow {
        color: var(--accent-purple);
    }

    .sensor-card.td {
        --card-accent: var(--accent-orange);
    }

    .sensor-card.td::before {
        background: var(--accent-orange);
    }

    .sensor-card.td .s-icon {
        color: var(--accent-orange);
    }

    .sensor-card.td .s-value.glow {
        color: var(--accent-orange);
    }

    /* Connection Panel */
    .conn-panel {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: var(--shadow-card);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .conn-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .conn-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .78rem;
        color: var(--text-secondary);
    }

    .conn-item i {
        font-size: .85rem;
    }

    .conn-item .val {
        color: var(--text-primary);
        font-weight: 600;
    }

    .conn-right .btn-refresh {
        background: var(--accent-blue);
        border: none;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 10px;
        cursor: pointer;
        font-size: .8rem;
        font-weight: 600;
        transition: all .2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .conn-right .btn-refresh:hover {
        background: #2B4DC9;
    }

    .conn-right .btn-refresh:active {
        transform: scale(.96);
    }

    .conn-right .btn-refresh .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, .3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .6s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Toast Notification */
    .mon-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: .85rem;
        font-weight: 600;
        color: #fff;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .15);
        transform: translateY(20px);
        opacity: 0;
        transition: all .3s ease;
        pointer-events: none;
    }

    .mon-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .mon-toast.toast-success {
        background: var(--accent-green);
    }

    .mon-toast.toast-error {
        background: var(--accent-red);
    }

    .mon-toast.toast-info {
        background: var(--accent-blue);
    }

    /* Animated Background Mesh */
    .bg-mesh {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
        pointer-events: none;
    }

    .bg-mesh .mesh-glow {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: .08;
        animation: meshFloat 10s ease-in-out infinite alternate;
    }

    .bg-mesh .mesh-glow:nth-child(1) {
        width: 500px;
        height: 500px;
        top: -10%;
        left: -5%;
        background: var(--accent-blue);
    }

    .bg-mesh .mesh-glow:nth-child(2) {
        width: 400px;
        height: 400px;
        bottom: -8%;
        right: -5%;
        background: var(--accent-purple);
        animation-duration: 14s;
        animation-delay: -4s;
    }

    .bg-mesh .mesh-glow:nth-child(3) {
        width: 300px;
        height: 300px;
        top: 40%;
        left: 40%;
        background: var(--accent-green);
        animation-duration: 12s;
        animation-delay: -8s;
        opacity: .04;
    }

    @keyframes meshFloat {
        0% {
            transform: translate(0, 0) scale(1);
        }

        33% {
            transform: translate(40px, -30px) scale(1.1);
        }

        66% {
            transform: translate(-20px, 40px) scale(.9);
        }

        100% {
            transform: translate(30px, -10px) scale(1.05);
        }
    }

    /* WiFi Signal Visual */
    .wifi-signal {
        display: inline-flex;
        align-items: flex-end;
        gap: 3px;
        margin-left: 4px;
    }

    .wifi-signal .bar {
        width: 4px;
        border-radius: 2px;
        background: var(--card-border);
        transition: all .6s ease;
    }

    .wifi-signal .bar:nth-child(1) {
        height: 6px;
    }

    .wifi-signal .bar:nth-child(2) {
        height: 10px;
    }

    .wifi-signal .bar:nth-child(3) {
        height: 14px;
    }

    .wifi-signal .bar:nth-child(4) {
        height: 18px;
    }

    .wifi-signal.online .bar {
        background: var(--accent-green);
    }

    .wifi-signal.online .bar:nth-child(1) {
        animation: barPulse 1.6s ease-in-out infinite;
    }

    .wifi-signal.online .bar:nth-child(2) {
        animation: barPulse 1.6s ease-in-out .2s infinite;
    }

    .wifi-signal.online .bar:nth-child(3) {
        animation: barPulse 1.6s ease-in-out .4s infinite;
    }

    .wifi-signal.online .bar:nth-child(4) {
        animation: barPulse 1.6s ease-in-out .6s infinite;
    }

    @keyframes barPulse {

        0%,
        100% {
            opacity: .4;
        }

        50% {
            opacity: 1;
        }
    }

    /* Heartbeat Pulse Ring */
    .heartbeat-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid transparent;
        pointer-events: none;
    }

    .heartbeat-ring.online {
        border-color: rgba(47, 158, 68, .2);
        animation: hbPulse 2s ease-out infinite;
    }

    @keyframes hbPulse {
        0% {
            transform: scale(1);
            opacity: .6;
        }

        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }

    .heartbeat-ring.online::before,
    .heartbeat-ring.online::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid rgba(47, 158, 68, .15);
        animation: hbPulse 2s ease-out infinite .6s;
    }

    .heartbeat-ring.online::after {
        animation-delay: 1.2s;
    }

    /* Live Event Log */
    .event-log-section {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 14px;
        padding: 20px 24px;
        box-shadow: var(--shadow-card);
        margin-bottom: 24px;
    }

    .event-log-title {
        font-size: .85rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .event-log-title i {
        color: var(--accent-blue);
    }

    .event-log-title .log-count {
        font-size: .72rem;
        color: var(--text-muted);
        font-weight: 400;
    }

    .event-log {
        max-height: 200px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .event-log::-webkit-scrollbar {
        width: 4px;
    }

    .event-log::-webkit-scrollbar-track {
        background: transparent;
    }

    .event-log::-webkit-scrollbar-thumb {
        background: var(--card-border);
        border-radius: 4px;
    }

    .event-log-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: .78rem;
        color: var(--text-secondary);
        background: #F8F9FB;
        border-left: 3px solid var(--card-border);
        animation: logSlide .3s ease-out;
    }

    .event-log-item .log-time {
        font-family: 'JetBrains Mono', monospace;
        font-size: .7rem;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .event-log-item .log-icon {
        flex-shrink: 0;
        width: 16px;
        text-align: center;
    }

    .event-log-item .log-msg {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .event-log-item.level-online {
        border-left-color: var(--accent-green);
    }

    .event-log-item.level-online .log-icon {
        color: var(--accent-green);
    }

    .event-log-item.level-scanning {
        border-left-color: var(--accent-yellow);
    }

    .event-log-item.level-scanning .log-icon {
        color: var(--accent-yellow);
    }

    .event-log-item.level-measuring {
        border-left-color: var(--accent-cyan);
    }

    .event-log-item.level-measuring .log-icon {
        color: var(--accent-cyan);
    }

    .event-log-item.level-error {
        border-left-color: var(--accent-red);
    }

    .event-log-item.level-error .log-icon {
        color: var(--accent-red);
    }

    .event-log-item.level-completed {
        border-left-color: var(--accent-purple);
    }

    .event-log-item.level-completed .log-icon {
        color: var(--accent-purple);
    }

    @keyframes logSlide {
        from {
            opacity: 0;
            transform: translateX(-12px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Empty State */
    .empty-monitor {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-monitor i {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 12px;
        display: block;
    }

    .empty-monitor h3 {
        color: var(--text-primary);
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 6px;
    }

    .empty-monitor p {
        color: var(--text-secondary);
        font-size: .85rem;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sensor-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .main-status {
            padding: 24px;
        }

        .main-status-inner {
            flex-direction: column;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .sensor-grid {
            grid-template-columns: 1fr 1fr;
        }

        .mon-header-left h1 {
            font-size: 1.2rem;
        }
    }
</style>

<div class="mon-dash">
    <div class="bg-mesh">
        <div class="mesh-glow"></div>
        <div class="mesh-glow"></div>
        <div class="mesh-glow"></div>
    </div>
    <div class="mon-header">
        <div class="mon-header-left">
            <h1><i class="bi bi-motherboard-fill"></i> Monitoring Alat</h1>
            <p><i class="bi bi-info-circle"></i> Status real-time alat Smart Healthy Checkup IoT</p>
        </div>
        <div class="mon-header-right">
            <span class="last-update" id="lastUpdate"><i class="bi bi-clock"></i> Menunggu data...</span>
            <span class="status-pill offline" id="statusPill">
                <span class="dot"></span>
                <span id="statusLabel">Offline</span>
            </span>
        </div>
    </div>

    <div id="monitorContent">
        <div class="empty-monitor">
            <i class="bi bi-wifi-off"></i>
            <h3>Menunggu Koneksi Alat</h3>
            <p>Nyalakan alat IoT dan pastikan terhubung ke server untuk melihat status real-time.</p>
        </div>
    </div>

    <div class="conn-panel">
        <div class="conn-left">
            <div class="conn-item"><i class="bi bi-wifi"></i> WiFi: <span class="val" id="connWifi">—</span> <span
                    class="wifi-signal offline" id="wifiSignal"><span class="bar"></span><span class="bar"></span><span
                        class="bar"></span><span class="bar"></span></span></div>
            <div class="conn-item"><i class="bi bi-hdd-network"></i> Server: <span class="val"
                    id="connServer">smartmedic.fikrisan.net</span></div>
            <div class="conn-item"><i class="bi bi-arrow-repeat"></i> Polling: <span class="val" id="connPolling">2
                    detik</span></div>
        </div>
        <div class="conn-right">
            <button class="btn-refresh" onclick="refreshWithFeedback()"><i class="bi bi-arrow-clockwise"></i>
                Refresh</button>
        </div>
    </div>
</div>

<div class="mon-toast" id="monToast"></div>

<script>
    let lastStatus = null;
    let eventLog = [];

    function addLog(status, msg) {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        eventLog.unshift({ time, status, msg });
        if (eventLog.length > 50) eventLog.pop();
    }

    function renderEventLog() {
        if (!eventLog.length) return '';
        const icons = { online: 'bi-wifi', scanning: 'bi-upc-scan', verified: 'bi-person-check', measuring: 'bi-activity', error: 'bi-exclamation-triangle', completed: 'bi-check-all' };
        let html = '<div class="event-log-section"><div class="event-log-title"><span><i class="bi bi-justify-left"></i> Event Log</span><span class="log-count">' + eventLog.length + ' event</span></div><div class="event-log">';
        eventLog.slice(0, 20).forEach(function (e) {
            const icon = icons[e.status] || 'bi-info-circle';
            html += '<div class="event-log-item level-' + e.status + '"><span class="log-time">' + e.time + '</span><span class="log-icon"><i class="bi ' + icon + '"></i></span><span class="log-msg">' + escapeHtml(e.msg) + '</span></div>';
        });
        html += '</div></div>';
        return html;
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function updateWifiSignal(status) {
        const ws = document.getElementById('wifiSignal');
        ws.className = 'wifi-signal ' + (status === 'offline' ? 'offline' : 'online');
    }

    function showToast(msg, type) {
        const t = document.getElementById('monToast');
        t.textContent = msg;
        t.className = 'mon-toast toast-' + type + ' show';
        setTimeout(function () { t.classList.remove('show'); }, 3000);
    }

    function resetRefreshBtn() {
        const btn = document.querySelector('.btn-refresh');
        if (!btn) return;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh';
        btn.disabled = false;
    }

    function refreshWithFeedback() {
        const btn = document.querySelector('.btn-refresh');
        btn.innerHTML = '<span class="spinner"></span> Memperbarui...';
        btn.disabled = true;
        fetchMonitor();
        setTimeout(resetRefreshBtn, 3000);
    }

    function fetchMonitor() {
        fetch('../../../../api/ambil_monitor.php?t=' + Date.now())
            .then(r => r.json())
            .then(data => {
                if (!data.waktu) {
                    document.getElementById('monitorContent').innerHTML = `
                    <div class="empty-monitor">
                        <i class="bi bi-wifi-off"></i>
                        <h3>Menunggu Koneksi Alat</h3>
                        <p>Nyalakan alat IoT dan pastikan terhubung ke server untuk melihat status real-time.</p>
                    </div>`;
                    updatePill('offline', 'Offline');
                    document.getElementById('lastUpdate').innerHTML = '<i class="bi bi-clock"></i> Belum ada data';
                    lastStatus = null;
                    resetRefreshBtn();
                    return;
                }

                const wifiVal = data.wifi && data.wifi !== '—' ? data.wifi : '—';
                document.getElementById('connWifi').textContent = wifiVal;
                updateWifiSignal(data.status);

                if (data.status === 'offline') {
                    document.getElementById('monitorContent').innerHTML = renderOfflineView(data) + renderEventLog();
                    updatePill('offline', 'Offline');
                    lastStatus = data;
                    resetRefreshBtn();
                    return;
                }

                const diff = data.time_ago || 999;
                const timeStr = diff < 5 ? 'Baru saja' : diff < 60 ? diff + ' detik lalu' : Math.floor(diff / 60) + ' menit lalu';
                document.getElementById('lastUpdate').innerHTML = '<i class="bi bi-clock"></i> ' + timeStr;

                // Stale detection: online idle → 15s, sedang proses → 90s
                const staleThreshold = data.status === 'online' ? 15 : 90;
                if (diff > staleThreshold && data.status !== 'offline') {
                    data.status = 'offline';
                    data.pesan = 'Alat tidak merespon — terakhir ' + timeStr;
                }

                // Jika offline (dari DB atau stale), render offline view
                if (data.status === 'offline') {
                    document.getElementById('monitorContent').innerHTML = renderOfflineView(data) + renderEventLog();
                    updatePill('offline', 'Offline');
                    lastStatus = data;
                    resetRefreshBtn();
                    return;
                }

                updatePill(data.status, data.status === 'online' ? 'Online' : data.status.charAt(0).toUpperCase() + data.status.slice(1));

                let statusRingClass = data.status;
                let statusIcon = 'bi-wifi';
                let statusTitle = 'Sistem Siap';
                let statusDesc = 'Menunggu kartu RFID untuk memulai pemeriksaan';
                let pesanHtml = '';

                switch (data.status) {
                    case 'online':
                        statusIcon = 'bi-wifi';
                        statusTitle = 'Sistem Siap';
                        statusDesc = 'Menunggu kartu RFID untuk memulai pemeriksaan';
                        break;
                    case 'scanning':
                        statusIcon = 'bi-rfid';
                        statusTitle = 'Kartu Terdeteksi!';
                        statusDesc = data.nama ? 'Memeriksa data mahasiswa: ' + data.nama : 'Memproses kartu RFID...';
                        break;
                    case 'verified':
                        statusIcon = 'bi-person-check-fill';
                        statusTitle = 'Mahasiswa Terverifikasi';
                        statusDesc = data.nama + ' — memulai pengukuran';
                        break;
                    case 'measuring':
                        statusIcon = 'bi-activity';
                        statusTitle = 'Pengukuran Sedang Berlangsung';
                        statusDesc = 'Tahap ' + data.tahap.toUpperCase().replace('_', ' ');
                        break;
                    case 'error':
                        statusIcon = 'bi-exclamation-triangle-fill';
                        statusTitle = 'Kartu Tidak Dikenal!';
                        statusDesc = data.pesan || 'Kartu belum terdaftar di sistem';
                        break;
                    case 'completed':
                        statusIcon = 'bi-check-all';
                        statusTitle = 'Pemeriksaan Selesai!';
                        statusDesc = 'Data berhasil dikirim ke server';
                        break;
                    default:
                        statusIcon = 'bi-wifi';
                        statusTitle = data.status;
                        statusDesc = data.pesan || '—';
                }

                if (data.pesan && data.pesan !== '—') {
                    pesanHtml = '<div class="pesan"><i class="bi bi-info-circle-fill"></i> ' + data.pesan + '</div>';
                }

                // Log event
                if (!lastStatus || lastStatus.status !== data.status || lastStatus.tahap !== data.tahap) {
                    const logMsg = data.nama ? data.nama + ' — ' + (data.pesan || data.tahap.replace(/_/g, ' ')) : (data.pesan || data.tahap.replace(/_/g, ' '));
                    addLog(data.status, logMsg);
                }

                const statusHtml = `
                <div class="main-status">
                    <div class="main-status-inner">
                        <div class="status-ring">
                            <div class="status-ring-inner ${statusRingClass}">
                                <i class="bi ${statusIcon}"></i>
                                <div class="heartbeat-ring ${statusRingClass}"></div>
                            </div>
                        </div>
                        <div class="status-text">
                            <h2>${statusTitle}</h2>
                            <p>${statusDesc}</p>
                            ${pesanHtml}
                        </div>
                    </div>
                </div>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="label"><i class="bi bi-person-badge-fill"></i> Mahasiswa</div>
                        <div class="value"><span class="${data.nama ? 'nama-text' : 'empty-val'}">${data.nama || '—'}</span></div>
                    </div>
                    <div class="info-card">
                        <div class="label"><i class="bi bi-list-task"></i> Tahap</div>
                        <div class="value"><span class="${data.tahap && data.tahap !== '—' ? 'nama-text' : 'empty-val'}">${data.tahap && data.tahap !== '—' ? data.tahap.replace(/_/g, ' ').toUpperCase() : '—'} ${data.tahap && data.tahap !== '—' && data.status !== 'completed' && data.status !== 'online' ? '<span style="font-size:.7rem;color:var(--neon-yellow);">(sedang berjalan)</span>' : ''}</span></div>
                    </div>
                    <div class="info-card">
                        <div class="label"><i class="bi bi-upc-scan"></i> UID Kartu</div>
                        <div class="value"><span class="${data.uid ? 'uid-text' : 'empty-val'}">${data.uid || '—'}</span></div>
                    </div>
                </div>
                ${buildTimeline(data)}
                <div class="sensor-grid">
                    <div class="sensor-card suhu ${data.suhu ? 'has-value' : ''}">
                        <span class="s-icon">🌡</span>
                        <div class="s-label">Suhu Tubuh</div>
                        <div class="s-value ${data.suhu ? 'glow' : ''}">${data.suhu || '—'}</div>
                        <div class="s-status">${data.suhu ? kategoriSuhu(parseFloat(data.suhu)) : 'Menunggu'}</div>
                    </div>
                    <div class="sensor-card spo2 ${data.spo2 ? 'has-value' : ''}">
                        <span class="s-icon">🩸</span>
                        <div class="s-label">SpO2</div>
                        <div class="s-value ${data.spo2 ? 'glow' : ''}">${data.spo2 ? data.spo2 + '%' : '—'}</div>
                        <div class="s-status">${data.spo2 ? kategoriSpO2(parseInt(data.spo2)) : 'Menunggu'}</div>
                    </div>
                    <div class="sensor-card hr ${data.hr ? 'has-value' : ''}">
                        <span class="s-icon">💓</span>
                        <div class="s-label">Detak Jantung</div>
                        <div class="s-value ${data.hr ? 'glow' : ''}">${data.hr ? data.hr + ' bpm' : '—'}</div>
                        <div class="s-status">${data.hr ? kategoriHR(parseInt(data.hr)) : 'Menunggu'}</div>
                    </div>
                    <div class="sensor-card td ${data.td ? 'has-value' : ''}">
                        <span class="s-icon">🩺</span>
                        <div class="s-label">Tekanan Darah</div>
                        <div class="s-value ${data.td ? 'glow' : ''}">${data.td || '—'}</div>
                        <div class="s-status">${data.td ? kategoriTD(data.td) : 'Menunggu'}</div>
                    </div>
                </div>
                ${renderEventLog()}
            `;

                document.getElementById('monitorContent').innerHTML = statusHtml;
                lastStatus = data;
                resetRefreshBtn();
            })
            .catch(function () {
                showToast('Gagal terhubung ke server', 'error');
                document.getElementById('monitorContent').innerHTML = `
                <div class="empty-monitor">
                    <i class="bi bi-exclamation-triangle-fill" style="color:var(--accent-red);"></i>
                    <h3>Gagal Terhubung ke Server</h3>
                    <p>Periksa koneksi database atau refresh halaman.</p>
                </div>`;
                resetRefreshBtn();
            });
    }

    function buildTimeline(data) {
        const steps = [
            { id: 'standby', label: 'Menunggu Kartu', desc: 'Sistem siap — tap kartu RFID untuk memulai', icon: 'bi-rfid' },
            { id: 'scan', label: 'Verifikasi Kartu', desc: 'Memeriksa UID ke server', icon: 'bi-search' },
            { id: 'suhu', label: 'Suhu Tubuh', desc: 'Mengukur suhu dengan sensor MLX90614', icon: 'bi-thermometer-half' },
            { id: 'spo2', label: 'SpO2', desc: 'Mengukur oksigen darah dengan MAX30102', icon: 'bi-droplet-half' },
            { id: 'tensi', label: 'Tekanan Darah', desc: 'Mengukur tensi dengan Aristoteles Watch', icon: 'bi-heart-pulse' },
            { id: 'kirim', label: 'Kirim Data', desc: 'Mengirim hasil ke server', icon: 'bi-cloud-upload' },
            { id: 'selesai', label: 'Selesai', desc: 'Pemeriksaan selesai', icon: 'bi-check-circle' }
        ];

        let currentStep = 'standby';
        const tahap = data.tahap || '';
        const status = data.status || '';

        if (status === 'completed') currentStep = 'selesai';
        else if (status === 'measuring') {
            if (tahap.includes('suhu')) currentStep = 'suhu';
            else if (tahap.includes('spo2')) currentStep = 'spo2';
            else if (tahap.includes('tensi') || tahap.includes('td')) currentStep = 'tensi';
            else if (tahap.includes('kirim')) currentStep = 'kirim';
        }
        else if (status === 'scanning' || status === 'verified') currentStep = 'scan';

        let found = false;
        let html = '<div class="timeline-section"><div class="timeline-title"><i class="bi bi-list-check"></i> Progres Pemeriksaan</div><div class="timeline">';

        steps.forEach((step, idx) => {
            let state = 'pending';
            if (step.id === currentStep) { state = 'active'; found = true; }
            else if (!found) state = 'done';

            const icons = { 'standby': 'bi-rfid', 'scan': 'bi-upc-scan', 'suhu': 'bi-thermometer-half', 'spo2': 'bi-droplet-half', 'tensi': 'bi-heart-pulse', 'kirim': 'bi-cloud-upload', 'selesai': 'bi-check-circle' };
            const dotIcon = icons[step.id] || 'bi-circle';

            let badgeHtml = '';
            if (state === 'done') badgeHtml = '<span class="tl-badge on"><i class="bi bi-check"></i> Selesai</span>';
            else if (state === 'active') badgeHtml = '<span class="tl-badge now"><i class="bi bi-arrow-repeat"></i> Sedang berjalan...</span>';
            else badgeHtml = '<span class="tl-badge wait"><i class="bi bi-hourglass"></i> Menunggu</span>';

            let valueHtml = '';
            if (state === 'done' || state === 'active') {
                if (step.id === 'suhu' && data.suhu) valueHtml = '<div class="tl-value">' + data.suhu + ' °C</div>';
                else if (step.id === 'spo2' && data.spo2) valueHtml = '<div class="tl-value">' + data.spo2 + ' %</div>';
                else if (step.id === 'tensi' && data.td) valueHtml = '<div class="tl-value">' + data.td + ' mmHg</div>';
                else if (step.id === 'scan' && data.uid) valueHtml = '<div class="tl-value" style="font-size:.85rem;color:var(--neon-yellow);">UID: ' + data.uid + '</div>';
            }

            html += `
            <div class="timeline-item ${state}">
                <div class="timeline-dot ${state}"><i class="bi ${dotIcon}"></i></div>
                <div class="timeline-content">
                    <h4>${step.label}</h4>
                    <p>${step.desc}</p>
                    ${badgeHtml}
                    ${valueHtml}
                </div>
            </div>`;
        });

        html += '</div></div>';
        return html;
    }

    function updatePill(status, label) {
        const pill = document.getElementById('statusPill');
        const lbl = document.getElementById('statusLabel');
        pill.className = 'status-pill ' + status;
        lbl.textContent = label;
    }

    function renderOfflineView(data) {
        return `
        <div class="main-status" style="border-color: #FFC9C9;">
            <div class="main-status-inner">
                <div class="status-ring">
                    <div class="status-ring-inner offline">
                        <i class="bi bi-wifi-off"></i>
                        <div class="heartbeat-ring"></div>
                    </div>
                </div>
                <div class="status-text">
                    <h2 style="color:var(--accent-red);">Alat Offline</h2>
                    <p style="color:var(--text-secondary);">${data.pesan || 'Alat tidak terhubung ke server'}</p>
                    <div class="pesan"><i class="bi bi-info-circle-fill"></i> Periksa koneksi WiFi dan power alat</div>
                </div>
            </div>
        </div>
        <div class="info-grid">
            <div class="info-card">
                <div class="label"><i class="bi bi-person-badge-fill"></i> Data Terakhir</div>
                <div class="value"><span class="${data.nama ? 'nama-text' : 'empty-val'}">${data.nama || '—'}</span></div>
            </div>
            <div class="info-card">
                <div class="label"><i class="bi bi-list-task"></i> Tahap Terakhir</div>
                <div class="value"><span class="empty-val">${data.tahap && data.tahap !== '—' ? data.tahap.replace(/_/g, ' ') : '—'}</span></div>
            </div>
            <div class="info-card">
                <div class="label"><i class="bi bi-upc-scan"></i> UID</div>
                <div class="value"><span class="${data.uid ? 'uid-text' : 'empty-val'}">${data.uid || '—'}</span></div>
            </div>
        </div>`;
    }

    function kategoriSuhu(v) {
        if (v <= 0) return 'Tidak Terbaca';
        if (v < 35) return 'Terlalu Rendah';
        if (v < 35.5) return 'Sub-Normal';
        if (v <= 37.5) return 'Normal';
        if (v <= 38.5) return 'Demam Ringan';
        if (v <= 39.5) return 'Demam Sedang';
        if (v <= 40) return 'Demam Tinggi';
        return 'Gawat Darurat';
    }
    function kategoriSpO2(v) {
        if (v <= 0) return 'Tidak Terbaca';
        if (v < 85) return 'DARURAT';
        if (v < 90) return 'Rendah';
        if (v < 95) return 'Rendah Ringan';
        return 'Normal';
    }
    function kategoriHR(v) {
        if (v <= 0) return 'Tidak Terbaca';
        if (v < 60) return 'Terlalu Lambat';
        if (v <= 100) return 'Normal';
        return 'Terlalu Cepat';
    }
    function kategoriTD(td) {
        if (!td || td === '0/0') return 'Tidak Terbaca';
        const parts = td.split('/');
        const s = parseInt(parts[0]), d = parseInt(parts[1]);
        if (s <= 0 || d <= 0) return 'Tidak Terbaca';
        if (s >= 180 || d >= 120) return 'KRISIS TENSI';
        if (s >= 160 || d >= 100) return 'Hipertensi Stg 2';
        if (s >= 140 || d >= 90) return 'Hipertensi Stg 1';
        if (s >= 130 || d >= 85) return 'Normal Tinggi';
        return 'Normal';
    }

    // Auto polling every 2 seconds
    let pollInterval = setInterval(fetchMonitor, 2000);
    fetchMonitor();

    // Pause polling when tab is hidden
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            clearInterval(pollInterval);
        } else {
            fetchMonitor();
            pollInterval = setInterval(fetchMonitor, 2000);
        }
    });
</script>

<?php require_once '../../../../include/footer.php'; ?>