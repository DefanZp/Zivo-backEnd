<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Zivo API</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f5f5f5;
            color: #171717;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 760px;
            background: #ffffff;
            border: 1px solid #e5e5e5;
            border-radius: 20px;
            padding: 48px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
        }

        .logo {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            border-radius: 12px;
            background: #111111;
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
        }

        h1 {
            font-size: 40px;
            line-height: 1.1;
            margin-bottom: 12px;
        }

        .description {
            color: #737373;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .section {
            margin-top: 32px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #737373;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 14px;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: #f0fdf4;
            color: #15803d;
            font-size: 14px;
            font-weight: 600;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
        }

        .version {
            font-size: 16px;
            color: #404040;
        }

        .endpoints {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .endpoint {
            padding: 16px;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            background: #fafafa;
            font-size: 15px;
            font-weight: 500;
        }

        .footer {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #e5e5e5;
            color: #a3a3a3;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 32px 24px;
            }

            h1 {
                font-size: 32px;
            }

            .description {
                font-size: 16px;
            }

            .endpoints {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <main class="container">

        <div class="logo">
            Z
        </div>

        <h1>Zivo API</h1>

        <p class="description">
            Backend REST API for Zivo E-Commerce Platform
        </p>

        <div class="section">
            <div class="section-title">
                Status
            </div>

            <div class="status">
                <span class="status-dot"></span>
                Operational
            </div>
        </div>

        <div class="section">
            <div class="section-title">
                Version
            </div>

            <p class="version">
                1.0.0
            </p>
        </div>

        <div class="section">
            <div class="section-title">
                Endpoints
            </div>

            <div class="endpoints">
                <div class="endpoint">Products</div>
                <div class="endpoint">Authentication</div>
                <div class="endpoint">Orders</div>
                <div class="endpoint">Payments</div>
                <div class="endpoint">Shipping</div>
            </div>
        </div>

        <div class="footer">
            Built with Laravel 12
        </div>

    </main>

</body>
</html>