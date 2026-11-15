@php
    $appName = config('app.name', 'Laravel');
    if ($appName === 'Laravel') {
        $appName = 'Amar Hisab';
    }
    $apiBaseUrl = url('/api/v1');
    $dbIsConnected = $dbConnected ?? false;
    $dbDatabaseName = $dbName ?? config('database.connections.' . config('database.default') . '.database', 'Database');
    $serverTime = now()->format('Y-m-d H:i:s T');
    $laravelVersion = app()->version();
    $phpVersion = PHP_VERSION;
    $env = app()->environment();
    $isDebug = config('app.debug');
@endphp
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} — Official API Gateway & Services</title>
    <meta name="description" content="Official high-performance RESTful API backend for {{ $appName }}, powering modern Web and Mobile client applications.">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polygon points='12 2 2 7 12 12 22 7 12 2'/><polyline points='2 17 12 22 22 17'/><polyline points='2 12 12 17 22 12'/></svg>">
    
    <!-- Preconnect & Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-body: #080C15;
            --bg-card: rgba(15, 23, 42, 0.7);
            --bg-card-hover: rgba(30, 41, 59, 0.8);
            --bg-card-solid: #0F172A;
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(99, 102, 241, 0.35);
            --primary: #6366F1;
            --primary-light: #818CF8;
            --primary-glow: rgba(99, 102, 241, 0.25);
            --emerald: #10B981;
            --emerald-glow: rgba(16, 185, 129, 0.25);
            --amber: #F59E0B;
            --sky: #0EA5E9;
            --purple: #A855F7;
            --rose: #F43F5E;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --text-faint: #64748B;
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-mono: 'Fira Code', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: var(--font-sans);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
            -webkit-font-smoothing: antialiased;
        }

        /* Ambient glowing background */
        .ambient-glow {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .glow-circle-1 {
            position: absolute;
            top: -150px;
            left: 15%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0) 70%);
            border-radius: 50%;
            filter: blur(40px);
        }
        .glow-circle-2 {
            position: absolute;
            top: 35%;
            right: -100px;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0) 70%);
            border-radius: 50%;
            filter: blur(50px);
        }
        .glow-grid {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            mask-image: radial-gradient(ellipse at 50% 20%, black 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at 50% 20%, black 40%, transparent 80%);
        }

        /* Layout wrapper */
        .app-container {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            width: 100%;
        }

        /* Navbar */
        nav.navbar {
            padding: 24px 0;
            border-bottom: 1px solid var(--border-subtle);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(8, 12, 21, 0.85);
        }
        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .brand-link {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--text-main);
        }
        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            flex-shrink: 0;
        }
        .brand-title {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .brand-badge {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 20px;
            background: rgba(99, 102, 241, 0.15);
            color: #A5B4FC;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .nav-link:hover {
            color: var(--text-main);
        }
        .nav-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--border-subtle);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .nav-cta:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        /* Pulse Status Tag */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 30px;
            color: #34D399;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #10B981;
            border-radius: 50%;
            position: relative;
        }
        .pulse-dot::after {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: 50%;
            background-color: #10B981;
            animation: pulse-ring 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 0.9; }
            70% { transform: scale(2.2); opacity: 0; }
            100% { transform: scale(2.2); opacity: 0; }
        }

        /* Hero Section */
        .hero {
            padding: 64px 0 48px;
            text-align: center;
        }
        .hero-top-badge {
            margin-bottom: 20px;
            display: inline-block;
        }
        .hero-title {
            font-size: clamp(2.4rem, 5vw, 3.8rem);
            font-weight: 800;
            letter-spacing: -0.035em;
            line-height: 1.15;
            margin-bottom: 20px;
        }
        .gradient-text {
            background: linear-gradient(135deg, #FFFFFF 20%, #A5B4FC 60%, #38BDF8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-subtitle {
            max-width: 680px;
            margin: 0 auto 36px;
            color: var(--text-muted);
            font-size: 1.125rem;
            font-weight: 400;
            line-height: 1.7;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 48px;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 28px;
            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
            color: #FFFFFF;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 10px;
            text-decoration: none;
            box-shadow: 0 4px 20px var(--primary-glow);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(99, 102, 241, 0.45);
            filter: brightness(1.1);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: rgba(30, 41, 59, 0.5);
            color: var(--text-main);
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid var(--border-subtle);
            backdrop-filter: blur(8px);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: rgba(30, 41, 59, 0.9);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Base URL Bar */
        .base-url-bar {
            max-width: 650px;
            margin: 0 auto;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-subtle);
            backdrop-filter: blur(12px);
            padding: 8px 12px 8px 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }
        .base-url-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-faint);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .base-url-code {
            font-family: var(--font-mono);
            font-size: 0.9rem;
            color: #38BDF8;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .copy-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .copy-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 48px;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 20px;
            backdrop-filter: blur(12px);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-2px);
            background: var(--bg-card-hover);
        }
        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .stat-icon.emerald { background: rgba(16, 185, 129, 0.12); color: #34D399; }
        .stat-icon.indigo { background: rgba(99, 102, 241, 0.12); color: #818CF8; }
        .stat-icon.sky { background: rgba(14, 165, 233, 0.12); color: #38BDF8; }
        .stat-icon.purple { background: rgba(168, 85, 247, 0.12); color: #C084FC; }
        .stat-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-value {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stat-desc {
            font-size: 0.8rem;
            color: var(--text-faint);
            margin-top: 4px;
        }

        /* Section Header */
        .section-header {
            margin-bottom: 24px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Interactive API Console / Tester */
        .console-container {
            background: #0B1120;
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), 0 0 30px rgba(99, 102, 241, 0.05);
            margin-bottom: 56px;
        }
        .console-topbar {
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid var(--border-subtle);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .console-dots {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .console-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .dot-red { background-color: #EF4444; }
        .dot-yellow { background-color: #F59E0B; }
        .dot-green { background-color: #10B981; }

        .console-input-row {
            padding: 16px 20px;
            background: rgba(15, 23, 42, 0.6);
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--border-subtle);
        }
        .method-select {
            background: rgba(15, 23, 42, 0.9);
            color: #34D399;
            border: 1px solid var(--border-subtle);
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 10px 14px;
            border-radius: 8px;
            outline: none;
            cursor: pointer;
        }
        .endpoint-input-wrapper {
            flex: 1;
            min-width: 260px;
            position: relative;
            display: flex;
            align-items: center;
        }
        .endpoint-input {
            width: 100%;
            background: rgba(10, 15, 30, 0.8);
            border: 1px solid var(--border-subtle);
            color: #F8FAFC;
            font-family: var(--font-mono);
            font-size: 0.9rem;
            padding: 10px 16px;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s ease;
        }
        .endpoint-input:focus {
            border-color: var(--primary);
        }
        .send-btn {
            background: #4F46E5;
            color: #FFFFFF;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 10px 22px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 10px rgba(79, 70, 229, 0.3);
        }
        .send-btn:hover {
            background: #4338CA;
            transform: translateY(-1px);
        }
        .send-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .console-meta-bar {
            padding: 10px 20px;
            background: rgba(10, 15, 30, 0.6);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--text-muted);
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            font-family: var(--font-mono);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
        }
        .status-200 { background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-400 { background: rgba(239, 68, 68, 0.2); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .status-idle { background: rgba(148, 163, 184, 0.15); color: #94A3B8; border: 1px solid rgba(148, 163, 184, 0.2); }

        .console-response-box {
            padding: 20px;
            max-height: 380px;
            overflow-y: auto;
            background: #060911;
        }
        .console-pre {
            font-family: var(--font-mono);
            font-size: 0.875rem;
            color: #E2E8F0;
            line-height: 1.6;
            white-space: pre-wrap;
            word-break: break-all;
        }

        /* Endpoints Catalog */
        .endpoints-section {
            margin-bottom: 64px;
        }
        .search-endpoints-input {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid var(--border-subtle);
            color: #FFFFFF;
            font-size: 0.875rem;
            padding: 10px 16px 10px 38px;
            border-radius: 10px;
            outline: none;
            width: 280px;
            transition: all 0.2s ease;
        }
        .search-endpoints-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 12px var(--primary-glow);
        }
        .search-wrapper {
            position: relative;
            display: inline-block;
        }
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-faint);
            pointer-events: none;
        }

        .endpoint-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            margin-bottom: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .endpoint-card:hover {
            border-color: rgba(99, 102, 241, 0.3);
            background: var(--bg-card-hover);
        }
        .endpoint-summary {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            cursor: pointer;
            user-select: none;
        }
        .endpoint-left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .method-pill {
            font-family: var(--font-mono);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            min-width: 58px;
            text-align: center;
            letter-spacing: 0.05em;
        }
        .method-get { background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.25); }
        .method-post { background: rgba(99, 102, 241, 0.15); color: #818CF8; border: 1px solid rgba(99, 102, 241, 0.25); }
        .method-put { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.25); }
        .method-delete { background: rgba(239, 68, 68, 0.15); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.25); }

        .endpoint-path {
            font-family: var(--font-mono);
            font-size: 0.95rem;
            font-weight: 600;
            color: #FFFFFF;
        }
        .endpoint-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .endpoint-badges {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tag-public {
            font-size: 0.7rem;
            font-weight: 600;
            color: #38BDF8;
            background: rgba(14, 165, 233, 0.12);
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
        .tag-auth {
            font-size: 0.7rem;
            font-weight: 600;
            color: #FBBF24;
            background: rgba(245, 158, 11, 0.12);
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .tag-rate {
            font-size: 0.7rem;
            font-weight: 600;
            color: #C084FC;
            background: rgba(168, 85, 247, 0.12);
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid rgba(168, 85, 247, 0.2);
        }

        .endpoint-details {
            padding: 0 20px 20px 20px;
            border-top: 1px solid var(--border-subtle);
            background: rgba(10, 15, 30, 0.5);
            display: none;
        }
        .endpoint-details.open {
            display: block;
        }
        .snippet-box {
            background: #060911;
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 12px;
            font-family: var(--font-mono);
            font-size: 0.825rem;
            color: #E2E8F0;
            position: relative;
        }

        /* Tabs for Quick Start Code */
        .code-tabs-section {
            margin-bottom: 64px;
        }
        .tabs-header {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 12px;
            margin-bottom: 16px;
            overflow-x: auto;
        }
        .tab-btn {
            background: transparent;
            color: var(--text-muted);
            border: none;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .tab-btn.active {
            background: rgba(99, 102, 241, 0.15);
            color: #A5B4FC;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .code-card {
            background: #0B1120;
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            overflow: hidden;
            position: relative;
        }
        .code-card-header {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid var(--border-subtle);
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .code-body {
            padding: 20px;
            font-family: var(--font-mono);
            font-size: 0.875rem;
            color: #E2E8F0;
            line-height: 1.6;
            overflow-x: auto;
        }

        /* Footer */
        footer.footer {
            margin-top: auto;
            border-top: 1px solid var(--border-subtle);
            padding: 36px 0;
            background: rgba(8, 12, 21, 0.95);
        }
        .footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .footer-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .footer-link {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .footer-link:hover {
            color: var(--text-main);
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .hero-title {
                font-size: 2.2rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .console-input-row {
                flex-direction: column;
                align-items: stretch;
            }
            .endpoint-summary {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient glowing backdrop -->
    <div class="ambient-glow">
        <div class="glow-circle-1"></div>
        <div class="glow-circle-2"></div>
        <div class="glow-grid"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="app-container">
            <div class="nav-content">
                <a href="{{ url('/') }}" class="brand-link">
                    <div class="brand-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                            <polyline points="2 17 12 22 22 17"/>
                            <polyline points="2 12 12 17 22 12"/>
                        </svg>
                    </div>
                    <div>
                        <div class="brand-title">
                            {{ $appName }}
                            <span class="brand-badge">REST API v1</span>
                        </div>
                    </div>
                </a>

                <div class="nav-links">
                    <a href="#endpoints" class="nav-link">Endpoints</a>
                    <a href="#console" class="nav-link">Live Console</a>
                    <a href="#quickstart" class="nav-link">Integration</a>
                    <a href="{{ url('/maintenance') }}" class="nav-link">Maintenance</a>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="status-pill">
                        <span class="pulse-dot"></span>
                        <span>API Online</span>
                    </div>
                    <button class="nav-cta" onclick="copyText('{{ $apiBaseUrl }}', this)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                        <span>Copy Base URL</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="app-container">

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-top-badge">
                <div class="status-pill" style="background: rgba(99, 102, 241, 0.1); border-color: rgba(99, 102, 241, 0.25); color: #A5B4FC;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="m18 16 4-4-4-4"/>
                        <path d="m6 8-4 4 4 4"/>
                        <path d="m14.5 4-5 16"/>
                    </svg>
                    <span>Backend Microservice & API Gateway</span>
                </div>
            </div>

            <h1 class="hero-title">
                Reliable & Scalable<br>
                <span class="gradient-text">Backend API Gateway</span>
            </h1>

            <p class="hero-subtitle">
                Official RESTful API engine for <strong>{{ $appName }}</strong>. Powering Next.js web applications and mobile clients with token-based Sanctum authentication, low latency, and robust financial data services.
            </p>

            <div class="hero-actions">
                <a href="#console" class="btn-primary" onclick="testEndpoint('/api/v1')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                    <span>Test Live Ping</span>
                </a>
                <a href="#endpoints" class="btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"/>
                        <line x1="8" y1="12" x2="21" y2="12"/>
                        <line x1="8" y1="18" x2="21" y2="18"/>
                        <line x1="3" y1="6" x2="3.01" y2="6"/>
                        <line x1="3" y1="12" x2="3.01" y2="12"/>
                        <line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                    <span>Explore Endpoints</span>
                </a>
                <a href="{{ url('/up') }}" target="_blank" class="btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                    <span>Health Probe (/up)</span>
                </a>
            </div>

            <!-- Base URL copy pill -->
            <div class="base-url-bar">
                <div style="display: flex; align-items: center; gap: 12px; overflow: hidden;">
                    <span class="base-url-label">BASE URL</span>
                    <span class="base-url-code" id="baseUrlText">{{ $apiBaseUrl }}</span>
                </div>
                <button class="copy-btn" onclick="copyText('{{ $apiBaseUrl }}', this)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                    </svg>
                    <span>Copy</span>
                </button>
            </div>
        </section>

        <!-- Diagnostics & System Status -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">API Status</span>
                    <div class="stat-icon emerald">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value" style="color: #34D399;">
                    <span>Healthy & Active</span>
                </div>
                <div class="stat-desc">Version 1.0.0 &bull; JSON Protocol</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Database Engine</span>
                    <div class="stat-icon {{ $dbIsConnected ? 'emerald' : 'rose' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <ellipse cx="12" cy="5" rx="9" ry="3"/>
                            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">
                    @if($dbIsConnected)
                        <span style="color: #34D399;">Connected</span>
                    @else
                        <span style="color: #F87171;">Disconnected</span>
                    @endif
                </div>
                <div class="stat-desc">Driver: {{ strtoupper($dbDriver ?? 'mysql') }} &bull; DB: {{ $dbDatabaseName }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Environment</span>
                    <div class="stat-icon indigo">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="2" width="20" height="8" rx="2" ry="2"/>
                            <rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
                            <line x1="6" y1="6" x2="6.01" y2="6"/>
                            <line x1="6" y1="18" x2="6.01" y2="18"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">
                    <span style="text-transform: capitalize;">{{ $env }}</span>
                </div>
                <div class="stat-desc">Laravel {{ $laravelVersion }} &bull; PHP {{ $phpVersion }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Server Time</span>
                    <div class="stat-icon sky">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value" style="font-size: 1.15rem; font-family: var(--font-mono);" id="liveClock">
                    {{ now()->format('H:i:s') }}
                </div>
                <div class="stat-desc">{{ now()->format('M d, Y') }} &bull; {{ config('app.timezone') }}</div>
            </div>
        </div>

        <!-- Live API Inspector / Console -->
        <section id="console" class="console-container">
            <div class="console-topbar">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div class="console-dots">
                        <span class="console-dot dot-red"></span>
                        <span class="console-dot dot-yellow"></span>
                        <span class="console-dot dot-green"></span>
                    </div>
                    <span style="font-size: 0.85rem; font-weight: 700; letter-spacing: 0.05em; color: var(--text-muted); text-transform: uppercase;">
                        Interactive Live API Tester
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 0.8rem; color: var(--text-faint);">Direct in-browser execution</span>
                </div>
            </div>

            <div class="console-input-row">
                <select id="requestMethod" class="method-select" onchange="syncMethodStyle()">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                </select>

                <div class="endpoint-input-wrapper">
                    <input type="text" id="requestEndpoint" class="endpoint-input" value="/api/v1" placeholder="/api/v1">
                </div>

                <button id="sendBtn" class="send-btn" onclick="executeLiveRequest()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                    <span>Send Request</span>
                </button>
            </div>

            <!-- Quick presets -->
            <div style="padding: 10px 20px; background: rgba(10, 15, 30, 0.4); display: flex; align-items: center; gap: 10px; overflow-x: auto; border-bottom: 1px solid var(--border-subtle);">
                <span style="font-size: 0.75rem; font-weight: 600; color: var(--text-faint); text-transform: uppercase;">Presets:</span>
                <button class="copy-btn" onclick="setPreset('GET', '/api/v1')">GET /api/v1</button>
                <button class="copy-btn" onclick="setPreset('GET', '/up')">GET /up</button>
                <button class="copy-btn" onclick="setPreset('GET', '/api/v1/auth/me')">GET /api/v1/auth/me</button>
                <button class="copy-btn" onclick="setPreset('POST', '/api/v1/auth/web/login')">POST /api/v1/auth/web/login</button>
                <button class="copy-btn" onclick="setPreset('POST', '/api/v1/auth/mobile/login')">POST /api/v1/auth/mobile/login</button>
            </div>

            <div class="console-meta-bar">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <span>Status: <span id="responseStatusBadge" class="status-badge status-idle">Ready</span></span>
                    <span>Latency: <span id="responseLatency" style="font-family: var(--font-mono); color: #F8FAFC;">--</span></span>
                </div>
                <div>
                    <button class="copy-btn" style="padding: 4px 8px; font-size: 0.75rem;" onclick="copyResponse()">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                        <span>Copy Output</span>
                    </button>
                </div>
            </div>

            <div class="console-response-box">
                <pre class="console-pre" id="consoleOutput">// Click "Send Request" or select a preset to execute live handshake...
{
  "notice": "Amar Hisab API Gateway is listening.",
  "endpoint": "/api/v1",
  "status": "ready"
}</pre>
            </div>
        </section>

        <!-- API Endpoints Catalog -->
        <section id="endpoints" class="endpoints-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="#6366F1">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                        <span>API Endpoints Directory</span>
                    </h2>
                    <p class="section-subtitle">Categorized routes available in the current v1 release.</p>
                </div>
                <div class="search-wrapper">
                    <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" id="endpointSearch" class="search-endpoints-input" placeholder="Search routes (e.g. login, me)..." onkeyup="filterEndpoints()">
                </div>
            </div>

            <!-- Endpoint List -->
            <div id="endpointList">

                <!-- Endpoint 1 -->
                <div class="endpoint-card" data-search="get api v1 health ping handshake">
                    <div class="endpoint-summary" onclick="toggleEndpointDetails(this)">
                        <div class="endpoint-left">
                            <span class="method-pill method-get">GET</span>
                            <span class="endpoint-path">/api/v1</span>
                            <span class="endpoint-desc">&mdash; API Handshake & Gateway Status</span>
                        </div>
                        <div class="endpoint-badges">
                            <span class="tag-public">Public</span>
                            <button class="copy-btn" onclick="event.stopPropagation(); setPreset('GET', '/api/v1'); scrollToConsole();">Test</button>
                        </div>
                    </div>
                    <div class="endpoint-details">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 12px;">
                            Returns a confirmation response indicating the API is healthy and reachable.
                        </div>
                        <div class="snippet-box">
curl -X GET "{{ $apiBaseUrl }}" \<br>
&nbsp;&nbsp;-H "Accept: application/json"
                        </div>
                    </div>
                </div>

                <!-- Endpoint 2 -->
                <div class="endpoint-card" data-search="get up framework healthcheck status">
                    <div class="endpoint-summary" onclick="toggleEndpointDetails(this)">
                        <div class="endpoint-left">
                            <span class="method-pill method-get">GET</span>
                            <span class="endpoint-path">/up</span>
                            <span class="endpoint-desc">&mdash; Framework Uptime & Container Probe</span>
                        </div>
                        <div class="endpoint-badges">
                            <span class="tag-public">Public</span>
                            <button class="copy-btn" onclick="event.stopPropagation(); setPreset('GET', '/up'); scrollToConsole();">Test</button>
                        </div>
                    </div>
                    <div class="endpoint-details">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 12px;">
                            Laravel 11+ built-in uptime health check, suitable for load balancers and Kubernetes health probes.
                        </div>
                        <div class="snippet-box">
curl -X GET "{{ url('/up') }}"
                        </div>
                    </div>
                </div>

                <!-- Endpoint 3 -->
                <div class="endpoint-card" data-search="post register auth user sign up">
                    <div class="endpoint-summary" onclick="toggleEndpointDetails(this)">
                        <div class="endpoint-left">
                            <span class="method-pill method-post">POST</span>
                            <span class="endpoint-path">/api/v1/auth/register</span>
                            <span class="endpoint-desc">&mdash; Register New User Account</span>
                        </div>
                        <div class="endpoint-badges">
                            <span class="tag-public">Public</span>
                            <span class="tag-rate">Throttle 5/min</span>
                        </div>
                    </div>
                    <div class="endpoint-details">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 12px;">
                            Creates a new account with email, name, and password validation.
                        </div>
                        <div class="snippet-box">
curl -X POST "{{ $apiBaseUrl }}/auth/register" \<br>
&nbsp;&nbsp;-H "Content-Type: application/json" \<br>
&nbsp;&nbsp;-H "Accept: application/json" \<br>
&nbsp;&nbsp;-d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'
                        </div>
                    </div>
                </div>

                <!-- Endpoint 4 -->
                <div class="endpoint-card" data-search="post auth web login session spa cookie">
                    <div class="endpoint-summary" onclick="toggleEndpointDetails(this)">
                        <div class="endpoint-left">
                            <span class="method-pill method-post">POST</span>
                            <span class="endpoint-path">/api/v1/auth/web/login</span>
                            <span class="endpoint-desc">&mdash; Web SPA Authentication (Session & Cookie)</span>
                        </div>
                        <div class="endpoint-badges">
                            <span class="tag-public">Public</span>
                            <span class="tag-rate">Throttle 5/min</span>
                        </div>
                    </div>
                    <div class="endpoint-details">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 12px;">
                            Authenticates web clients using Laravel Sanctum cookie session.
                        </div>
                        <div class="snippet-box">
curl -X POST "{{ $apiBaseUrl }}/auth/web/login" \<br>
&nbsp;&nbsp;-H "Content-Type: application/json" \<br>
&nbsp;&nbsp;-H "Accept: application/json" \<br>
&nbsp;&nbsp;-d '{"email":"user@example.com","password":"password"}'
                        </div>
                    </div>
                </div>

                <!-- Endpoint 5 -->
                <div class="endpoint-card" data-search="post auth mobile login bearer token sanctum">
                    <div class="endpoint-summary" onclick="toggleEndpointDetails(this)">
                        <div class="endpoint-left">
                            <span class="method-pill method-post">POST</span>
                            <span class="endpoint-path">/api/v1/auth/mobile/login</span>
                            <span class="endpoint-desc">&mdash; Mobile App Authentication (Bearer Token)</span>
                        </div>
                        <div class="endpoint-badges">
                            <span class="tag-public">Public</span>
                            <span class="tag-rate">Throttle 5/min</span>
                        </div>
                    </div>
                    <div class="endpoint-details">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 12px;">
                            Returns a personal access Bearer Token for mobile apps (Flutter / React Native).
                        </div>
                        <div class="snippet-box">
curl -X POST "{{ $apiBaseUrl }}/auth/mobile/login" \<br>
&nbsp;&nbsp;-H "Content-Type: application/json" \<br>
&nbsp;&nbsp;-H "Accept: application/json" \<br>
&nbsp;&nbsp;-d '{"email":"user@example.com","password":"password","device_name":"Pixel 8"}'
                        </div>
                    </div>
                </div>

                <!-- Endpoint 6 -->
                <div class="endpoint-card" data-search="get auth me profile user current sanctum">
                    <div class="endpoint-summary" onclick="toggleEndpointDetails(this)">
                        <div class="endpoint-left">
                            <span class="method-pill method-get">GET</span>
                            <span class="endpoint-path">/api/v1/auth/me</span>
                            <span class="endpoint-desc">&mdash; Get Current Authenticated Profile</span>
                        </div>
                        <div class="endpoint-badges">
                            <span class="tag-auth">auth:sanctum</span>
                        </div>
                    </div>
                    <div class="endpoint-details">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 12px;">
                            Returns profile and permission details for the authenticated user.
                        </div>
                        <div class="snippet-box">
curl -X GET "{{ $apiBaseUrl }}/auth/me" \<br>
&nbsp;&nbsp;-H "Accept: application/json" \<br>
&nbsp;&nbsp;-H "Authorization: Bearer YOUR_TOKEN_HERE"
                        </div>
                    </div>
                </div>

                <!-- Endpoint 7 -->
                <div class="endpoint-card" data-search="post auth web logout session">
                    <div class="endpoint-summary" onclick="toggleEndpointDetails(this)">
                        <div class="endpoint-left">
                            <span class="method-pill method-post">POST</span>
                            <span class="endpoint-path">/api/v1/auth/web/logout</span>
                            <span class="endpoint-desc">&mdash; Web Session Logout</span>
                        </div>
                        <div class="endpoint-badges">
                            <span class="tag-auth">auth:sanctum</span>
                        </div>
                    </div>
                    <div class="endpoint-details">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 12px;">
                            Invalidates the web user session and regenerates CSRF token.
                        </div>
                        <div class="snippet-box">
curl -X POST "{{ $apiBaseUrl }}/auth/web/logout" \<br>
&nbsp;&nbsp;-H "Accept: application/json"
                        </div>
                    </div>
                </div>

                <!-- Endpoint 8 -->
                <div class="endpoint-card" data-search="post auth mobile logout revoke token">
                    <div class="endpoint-summary" onclick="toggleEndpointDetails(this)">
                        <div class="endpoint-left">
                            <span class="method-pill method-post">POST</span>
                            <span class="endpoint-path">/api/v1/auth/mobile/logout</span>
                            <span class="endpoint-desc">&mdash; Mobile Token Revocation</span>
                        </div>
                        <div class="endpoint-badges">
                            <span class="tag-auth">auth:sanctum</span>
                        </div>
                    </div>
                    <div class="endpoint-details">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 12px;">
                            Revokes the active mobile Sanctum token.
                        </div>
                        <div class="snippet-box">
curl -X POST "{{ $apiBaseUrl }}/auth/mobile/logout" \<br>
&nbsp;&nbsp;-H "Accept: application/json" \<br>
&nbsp;&nbsp;-H "Authorization: Bearer YOUR_TOKEN_HERE"
                        </div>
                    </div>
                </div>

                <!-- Endpoint 9 -->
                <div class="endpoint-card" data-search="get maintenance artisan migrate cache clear devops">
                    <div class="endpoint-summary" onclick="toggleEndpointDetails(this)">
                        <div class="endpoint-left">
                            <span class="method-pill method-get" style="color: #F59E0B; background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.3);">TOOL</span>
                            <span class="endpoint-path">/maintenance</span>
                            <span class="endpoint-desc">&mdash; DevOps & Maintenance Control Center</span>
                        </div>
                        <div class="endpoint-badges">
                            <span class="tag-auth">Protected</span>
                            <a href="{{ url('/maintenance') }}" class="copy-btn" style="text-decoration: none;" onclick="event.stopPropagation()">Open Portal &rarr;</a>
                        </div>
                    </div>
                    <div class="endpoint-details">
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 12px;">
                            Web maintenance dashboard for running migrations, clearing route/view/config cache, and deployment optimization.
                        </div>
                        <div class="snippet-box">
{{ url('/maintenance') }}
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Developer Quick Start / Integration Snippets -->
        <section id="quickstart" class="code-tabs-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="#6366F1">
                            <polyline points="16 18 22 12 16 6"/>
                            <polyline points="8 6 2 12 8 18"/>
                        </svg>
                        <span>Client Integration Guide</span>
                    </h2>
                    <p class="section-subtitle">Plug into {{ $appName }} with your favorite language or framework.</p>
                </div>
            </div>

            <div class="tabs-header">
                <button class="tab-btn active" onclick="switchTab('tab-curl', this)">cURL</button>
                <button class="tab-btn" onclick="switchTab('tab-js', this)">JavaScript (Fetch)</button>
                <button class="tab-btn" onclick="switchTab('tab-dart', this)">Dart / Flutter</button>
                <button class="tab-btn" onclick="switchTab('tab-php', this)">PHP (Guzzle)</button>
                <button class="tab-btn" onclick="switchTab('tab-standards', this)">API Standards</button>
            </div>

            <!-- Tab: cURL -->
            <div id="tab-curl" class="tab-content active">
                <div class="code-card">
                    <div class="code-card-header">
                        <span style="font-size: 0.85rem; font-weight: 600; color: #A5B4FC;">Terminal / Bash</span>
                        <button class="copy-btn" onclick="copySnippet('code-curl', this)">Copy Snippet</button>
                    </div>
                    <div class="code-body">
                        <pre id="code-curl"># 1. Check API Health
curl -X GET "{{ $apiBaseUrl }}" \
  -H "Accept: application/json"

# 2. Authenticate Mobile Client (Returns Bearer Token)
curl -X POST "{{ $apiBaseUrl }}/auth/mobile/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"user@amarhisab.test","password":"secretpassword","device_name":"iPhone 15"}'

# 3. Access Protected User Profile
curl -X GET "{{ $apiBaseUrl }}/auth/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer &lt;YOUR_SANCTUM_TOKEN&gt;"</pre>
                    </div>
                </div>
            </div>

            <!-- Tab: JS -->
            <div id="tab-js" class="tab-content">
                <div class="code-card">
                    <div class="code-card-header">
                        <span style="font-size: 0.85rem; font-weight: 600; color: #A5B4FC;">Next.js / React / Browser Fetch</span>
                        <button class="copy-btn" onclick="copySnippet('code-js', this)">Copy Snippet</button>
                    </div>
                    <div class="code-body">
                        <pre id="code-js">const BASE_URL = '{{ $apiBaseUrl }}';

// Web SPA Login (with Sanctum Cookies)
export async function loginWeb(email, password) {
  // First ensure CSRF cookie is captured
  await fetch('{{ url('/sanctum/csrf-cookie') }}', { credentials: 'include' });

  const res = await fetch(`${BASE_URL}/auth/web/login`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    credentials: 'include',
    body: JSON.stringify({ email, password }),
  });
  return res.json();
}

// Fetch Profile
export async function getProfile() {
  const res = await fetch(`${BASE_URL}/auth/me`, {
    headers: { 'Accept': 'application/json' },
    credentials: 'include',
  });
  return res.json();
}</pre>
                    </div>
                </div>
            </div>

            <!-- Tab: Dart -->
            <div id="tab-dart" class="tab-content">
                <div class="code-card">
                    <div class="code-card-header">
                        <span style="font-size: 0.85rem; font-weight: 600; color: #A5B4FC;">Flutter / Dart HTTP</span>
                        <button class="copy-btn" onclick="copySnippet('code-dart', this)">Copy Snippet</button>
                    </div>
                    <div class="code-body">
                        <pre id="code-dart">import 'dart:convert';
import 'package:http/http.dart' as http;

class AmarHisabApi {
  static const String baseUrl = '{{ $apiBaseUrl }}';

  static Future&lt;Map&lt;String, dynamic&gt;&gt; loginMobile(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/mobile/login'),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: jsonEncode({
        'email': email,
        'password': password,
        'device_name': 'Flutter App',
      }),
    );
    return jsonDecode(response.body);
  }
}</pre>
                    </div>
                </div>
            </div>

            <!-- Tab: PHP -->
            <div id="tab-php" class="tab-content">
                <div class="code-card">
                    <div class="code-card-header">
                        <span style="font-size: 0.85rem; font-weight: 600; color: #A5B4FC;">PHP / Laravel Client</span>
                        <button class="copy-btn" onclick="copySnippet('code-php', this)">Copy Snippet</button>
                    </div>
                    <div class="code-body">
                        <pre id="code-php">use Illuminate\Support\Facades\Http;

$response = Http::acceptJson()
    ->withToken($sanctumToken)
    ->get('{{ $apiBaseUrl }}/auth/me');

if ($response->successful()) {
    $user = $response->json('data');
}</pre>
                    </div>
                </div>
            </div>

            <!-- Tab: Standards -->
            <div id="tab-standards" class="tab-content">
                <div class="code-card">
                    <div class="code-card-header">
                        <span style="font-size: 0.85rem; font-weight: 600; color: #A5B4FC;">Standard JSON Response Envelope</span>
                        <button class="copy-btn" onclick="copySnippet('code-standards', this)">Copy Spec</button>
                    </div>
                    <div class="code-body">
                        <pre id="code-standards">// Standard Success Response (HTTP 200/201)
{
  "success": true,
  "message": "Resource retrieved successfully.",
  "data": { ... }
}

// Standard Error Response (HTTP 400/404/422/500)
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}</pre>
                    </div>
                </div>
            </div>

        </section>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="app-container">
            <div class="footer-content">
                <div>
                    &copy; {{ date('Y') }} <strong>{{ $appName }}</strong>. All rights reserved. Built with Laravel {{ $laravelVersion }}.
                </div>
                <div class="footer-links">
                    <a href="{{ url('/api/v1') }}" class="footer-link">API Handshake</a>
                    <a href="{{ url('/up') }}" class="footer-link">Health Probe</a>
                    <a href="{{ url('/maintenance') }}" class="footer-link">DevOps Console</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Interactive JavaScript -->
    <script>
        // Copy to clipboard helper
        function copyText(text, element) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = element.innerHTML;
                element.innerHTML = `
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span style="color: #34D399;">Copied!</span>
                `;
                setTimeout(() => {
                    element.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        function copySnippet(elementId, btn) {
            const el = document.getElementById(elementId);
            if (!el) return;
            const text = el.innerText || el.textContent;
            copyText(text, btn);
        }

        function copyResponse() {
            const pre = document.getElementById('consoleOutput');
            const text = pre.innerText || pre.textContent;
            navigator.clipboard.writeText(text).then(() => {
                const statusBadge = document.getElementById('responseStatusBadge');
                const orig = statusBadge.textContent;
                statusBadge.textContent = 'Copied!';
                setTimeout(() => { statusBadge.textContent = orig; }, 1500);
            });
        }

        // Preset Handler
        function setPreset(method, endpoint) {
            document.getElementById('requestMethod').value = method;
            document.getElementById('requestEndpoint').value = endpoint;
            syncMethodStyle();
        }

        function scrollToConsole() {
            const el = document.getElementById('console');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function testEndpoint(endpoint) {
            setPreset('GET', endpoint);
            scrollToConsole();
            setTimeout(() => {
                executeLiveRequest();
            }, 300);
        }

        function syncMethodStyle() {
            const select = document.getElementById('requestMethod');
            if (select.value === 'POST') {
                select.style.color = '#818CF8';
            } else {
                select.style.color = '#34D399';
            }
        }

        // Live API Request Executor
        async function executeLiveRequest() {
            const method = document.getElementById('requestMethod').value;
            let endpoint = document.getElementById('requestEndpoint').value.trim();
            if (!endpoint.startsWith('/')) {
                endpoint = '/' + endpoint;
            }

            const sendBtn = document.getElementById('sendBtn');
            const statusBadge = document.getElementById('responseStatusBadge');
            const latencySpan = document.getElementById('responseLatency');
            const outputPre = document.getElementById('consoleOutput');

            sendBtn.disabled = true;
            sendBtn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite;">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                </svg>
                <span>Sending...</span>
            `;
            statusBadge.className = 'status-badge status-idle';
            statusBadge.textContent = 'Requesting...';
            latencySpan.textContent = '...';

            const startTime = performance.now();

            try {
                const options = {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                    }
                };

                if (method === 'POST') {
                    options.headers['Content-Type'] = 'application/json';
                    options.body = JSON.stringify({
                        email: "test@example.com",
                        password: "sample-password",
                        device_name: "Live Web Console"
                    });
                }

                const response = await fetch(endpoint, options);
                const duration = Math.round(performance.now() - startTime);

                latencySpan.textContent = `${duration} ms`;

                const contentType = response.headers.get('content-type') || '';
                let resultText = '';

                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    resultText = JSON.stringify(data, null, 2);
                } else {
                    resultText = await response.text();
                }

                statusBadge.textContent = `${response.status} ${response.statusText || 'OK'}`;
                if (response.ok) {
                    statusBadge.className = 'status-badge status-200';
                } else {
                    statusBadge.className = 'status-badge status-400';
                }

                outputPre.textContent = resultText;
            } catch (err) {
                const duration = Math.round(performance.now() - startTime);
                latencySpan.textContent = `${duration} ms`;
                statusBadge.className = 'status-badge status-400';
                statusBadge.textContent = 'Network Error';
                outputPre.textContent = `// Error executing request:\n${err.message}\n\nNote: If hitting a POST endpoint that requires CSRF or database seeds, check server logs or verify route throttle.`;
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                    <span>Send Request</span>
                `;
            }
        }

        // Endpoint Accordion Details Toggle
        function toggleEndpointDetails(element) {
            const parent = element.closest('.endpoint-card');
            const details = parent.querySelector('.endpoint-details');
            if (details) {
                details.classList.toggle('open');
            }
        }

        // Search / Filter Endpoints
        function filterEndpoints() {
            const query = document.getElementById('endpointSearch').value.toLowerCase();
            const cards = document.querySelectorAll('.endpoint-card');
            cards.forEach(card => {
                const searchData = card.getAttribute('data-search') || '';
                const text = card.textContent.toLowerCase();
                if (searchData.toLowerCase().includes(query) || text.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Code Tabs Switcher
        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            const target = document.getElementById(tabId);
            if (target) target.classList.add('active');
            btn.classList.add('active');
        }

        // Live Clock
        setInterval(() => {
            const clockEl = document.getElementById('liveClock');
            if (clockEl) {
                const now = new Date();
                clockEl.textContent = now.toTimeString().split(' ')[0];
            }
        }, 1000);
    </script>
</body>
</html>
