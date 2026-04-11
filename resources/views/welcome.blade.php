<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PAYSTRE</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,400,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background-color: #09090b;
                color: #ffffff;
                margin: 0;
            }
            .welcome-wrapper {
                min-height: 100vh;
                min-height: 100dvh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                position: relative;
                padding: 1.5rem;
                overflow: hidden;
            }
            .glow {
                position: absolute;
                top: -100px;
                left: 50%;
                transform: translateX(-50%);
                width: 500px;
                height: 400px;
                background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%);
                pointer-events: none;
            }
            .content {
                position: relative;
                z-index: 10;
                text-align: center;
                max-width: 480px;
                width: 100%;
            }
            .title {
                font-size: 4.5rem;
                font-weight: 700;
                letter-spacing: -0.025em;
                line-height: 1;
            }
            .title-pay { color: #ffffff; }
            .title-stre { color: #818cf8; }
            .description {
                margin-top: 1.5rem;
                font-size: 1.125rem;
                line-height: 1.75;
                color: #a1a1aa;
            }
            .btn-login {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                margin-top: 2.5rem;
                padding: 0.75rem 1.75rem;
                background-color: #6366f1;
                color: #ffffff;
                font-size: 0.875rem;
                font-weight: 600;
                border-radius: 0.5rem;
                text-decoration: none;
                transition: background-color 0.2s;
            }
            .btn-login:hover {
                background-color: #818cf8;
            }
            .btn-login:active {
                background-color: #4f46e5;
                transform: scale(0.97);
            }
            .btn-login svg {
                width: 1rem;
                height: 1rem;
            }
            .btn-logout {
                background: none;
                border: 1px solid #3f3f46;
                color: #a1a1aa;
                font-size: 0.8rem;
                font-weight: 500;
                padding: 0.5rem 1.25rem;
                border-radius: 0.5rem;
                cursor: pointer;
                transition: color 0.2s, border-color 0.2s;
                font-family: inherit;
            }
            .btn-logout:hover {
                color: #ffffff;
                border-color: #71717a;
            }
            .btn-logout:active {
                transform: scale(0.97);
            }
            .welcome-footer {
                position: relative;
                z-index: 10;
                margin-top: auto;
                padding-top: 3rem;
                padding-bottom: 1rem;
                font-size: 0.75rem;
                color: #52525b;
                text-align: center;
                width: 100%;
            }
            .welcome-footer .copyright {
                margin-bottom: 0.35rem;
            }
            .welcome-footer .developed {
                font-size: 0.65rem;
                color: #3f3f46;
            }

            /* Mobile */
            @media (max-width: 640px) {
                .welcome-wrapper {
                    padding: 1.25rem;
                    justify-content: center;
                }
                .glow {
                    width: 300px;
                    height: 250px;
                    top: -50px;
                }
                .title {
                    font-size: 3rem;
                }
                .description {
                    margin-top: 1rem;
                    font-size: 0.95rem;
                    line-height: 1.6;
                    padding: 0 0.5rem;
                }
                .btn-login {
                    margin-top: 2rem;
                    padding: 0.875rem 2rem;
                    font-size: 0.9rem;
                    width: 100%;
                    justify-content: center;
                    border-radius: 0.625rem;
                }
                .btn-logout {
                    width: 100%;
                    padding: 0.625rem;
                }
                .welcome-footer {
                    padding-top: 2rem;
                    padding-bottom: 0.75rem;
                }
                .welcome-footer .developed {
                    font-size: 0.6rem;
                }
            }
        </style>
    </head>
    <body class="antialiased font-sans">
        <div class="welcome-wrapper">
            <div class="glow"></div>

            <div class="content">
                <h1 class="title">
                    <span class="title-pay">PAY</span><span class="title-stre">STRE</span>
                </h1>

                <p class="description">
                    Sistema de gesti&oacute;n de suscripciones de streaming. Administra cuentas, clientes, pagos y notificaciones en un solo lugar.
                </p>

                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-login">
                        Ir al Dashboard
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin-top: 1rem;">
                        @csrf
                        <button type="submit" class="btn-logout">Cerrar Sesi&oacute;n</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-login">
                        Iniciar Sesi&oacute;n
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                @endauth
            </div>

            <footer class="welcome-footer">
                <div class="copyright">&copy; {{ date('Y') }} PAYSTRE</div>
                <div class="developed">Desarrollado por LMTF&reg;</div>
            </footer>
        </div>
    </body>
</html>
