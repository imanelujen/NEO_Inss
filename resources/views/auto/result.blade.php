<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat Devis Auto - Neo Assurances</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0F2F72 0%, #D889E3 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header .subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            font-weight: 300;
        }

        .main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideInUp 0.8s ease-out 0.2s both;
        }

        .card-header {
            background: linear-gradient(135deg, #D889E3 0%, #0F2F72 100%);
            padding: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .card-header .quote-id {
            opacity: 0.9;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }

        .content-grid {
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .info-section {
            grid-column: span 2;
        }

        .section-title {
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .section-title i {
            margin-right: 0.5rem;
            color: #4f46e5;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .info-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #4f46e5, #7c3aed);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #4f46e5;
        }

        .info-item:hover::before {
            transform: scaleY(1);
        }

        .info-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
        }

        .price-section {
            background: linear-gradient(135deg, #b91094ff 0%, #674dbbff 100%);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            margin: 2rem 0;
            position: relative;
            overflow: hidden;
        }

        .price-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: pulse 3s infinite;
        }

        .price-label {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .price-amount {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .price-period {
            font-size: 0.9rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .actions-section {
            padding: 0 2rem 2rem;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .email-form {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .email-form h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
            text-align: center;
        }

        .form-group {
            display: flex;
            gap: 1rem;
        }

        .email-input {
            flex: 1;
            padding: 0.875rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .email-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .btn-email {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 0.875rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-email:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
        }

        .footer-nav {
            text-align: center;
            margin-top: 2rem;
        }

        .footer-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }

        .footer-nav a:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-weight: 500;
            animation: slideInDown 0.5s ease-out;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.5;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .content-grid {
                grid-template-columns: 1fr;
                padding: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .form-group {
                flex-direction: column;
            }

            .price-amount {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 480px) {
            .card-header {
                padding: 1.5rem;
            }

            .content-grid {
                padding: 1rem;
            }

            .price-section {
                padding: 1.5rem;
            }

            .actions-section {
                padding: 0 1rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-car"></i> Votre Devis Auto result</h1>
            <p class="subtitle">Neo Assurances - Protection complète pour votre véhicule</p>
        </div>

        <!-- Success/Error Messages -->
        <!-- @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <ul style="margin-left: 1rem; margin-top: 0.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif -->

        <div class="main-card">
            <div class="card-header">
                <h2><i class="fas fa-file-contract"></i> Détails de votre devis</h2>
                <p class="quote-id">Devis #NEO-{{ date('Y') }}-001234</p>
            </div>

            <div class="content-grid">
                <div class="info-section">
                    <h3 class="section-title">
                        <i class="fas fa-car"></i>
                        Informations du véhicule
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Type de véhicule</div>
                            <div class="info-value">{{ $data['vehicle_type'] ?? 'Berline' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Marque</div>
                            <div class="info-value">{{ $data['make'] ?? 'Peugeot' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Modèle</div>
                            <div class="info-value">{{ $data['model'] ?? '308' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Année</div>
                            <div class="info-value">{{ $data['year'] ?? '2022' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Puissance fiscale</div>
                            <div class="info-value">{{ $data['puissance_fiscale'] ?? '6 CV' }}</div>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        Informations conducteur
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Date obtention de permis</div>
                            <div class="info-value">{{ $data['date_obtention_permis'] ?? '2018-03-15' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Bonus-Malus</div>
                            <div class="info-value">{{ $data['bonus_malus'] ?? '0.50' }}</div>
                        </div>
                    </div>
                </div>

                <div class="price-section">
                    <div class="price-label">Montant de votre assurance</div>
                    <div class="price-amount">{{ number_format($data['montant_base'] ?? 450, 0) }}DH</div>
                    <div class="price-period">par an • Paiement mensuel disponible</div>
                </div>
            </div>

            <div class="actions-section">
                <div class="action-buttons">
                    <a href="{{ route('auto.subscribe', ['devis_id' => $quote->id ?? 1]) }}" class="btn btn-primary">
                        <i class="fas fa-pen-to-square"></i>
                        Souscrire maintenant
                    </a>
                    <a href="{{ route('auto.download', ['devis_id' => $quote->id ?? 1]) }}" class="btn btn-secondary">
                        <i class="fas fa-download"></i>
                        Télécharger PDF
                    </a>
                </div>

                <div class="email-form">
                    <h3><i class="fas fa-envelope"></i> Recevoir par e-mail</h3>
                    <form action="{{ route('auto.email', ['devis_id' => $quote->id ?? 1]) }}" method="POST">
                        <!-- @csrf -->
                        <div class="form-group">
                            <input type="email"
                                   name="email"
                                   placeholder="votre.email@exemple.com"
                                   required
                                   class="email-input">
                            <button type="submit" class="btn-email">
                                <i class="fas fa-paper-plane"></i>
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="footer-nav">
            <a href="{{ route('auto.show', ['step' => 1]) }}">
                <i class="fas fa-arrow-left"></i>
                Nouveau Devis
            </a>
        </div>
    </div>

    <script>
        // Enhanced interactions
        document.querySelectorAll('.info-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.transform = 'translateY(-2px)';
            });
            item.addEventListener('mouseleave', () => {
                item.style.transform = 'translateY(0)';
            });
        });

        // Form submission feedback
        document.querySelector('.email-form form').addEventListener('submit', function(e) {
            const button = this.querySelector('.btn-email');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
            button.disabled = true;
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.info-item').forEach(item => {
            observer.observe(item);
        });
    </script>
</body>
</html>
