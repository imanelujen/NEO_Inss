<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis Assurance Auto - Neo Assurances</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --primary-pink: #D889E3;
            --light-pink: #E6A8F0;
            --soft-pink: #F4D4F7;
            --pale-pink: #FDEEFF;
            --primary-blue: #0F2F72;
            --light-blue: #1E4A8C;
            --soft-blue: #E8F0FF;
            --pale-blue: #F5F8FF;
            --white: #FFFFFF;
            --gray-50: #FAFAFA;
            --gray-100: #F5F5F5;
            --gray-200: #E5E5E5;
            --gray-300: #D4D4D4;
            --gray-600: #525252;
            --gray-700: #404040;
            --gray-800: #262626;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--pale-pink) 0%, var(--pale-blue) 100%);
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
            animation: fadeInDown 0.8s ease-out;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-pink), var(--primary-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .header .subtitle {
            color: var(--gray-600);
            font-size: 1.1rem;
            font-weight: 300;
        }

        /* Progress Bar */
        .progress-container {
            background: var(--white);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            animation: slideInUp 0.8s ease-out 0.2s both;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .progress-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 25px;
            left: 50%;
            width: 100%;
            height: 3px;
            background: var(--gray-200);
            z-index: 1;
        }

        .progress-step.active:not(:last-child)::after {
            background: linear-gradient(90deg, var(--primary-pink), var(--primary-blue));
        }

        .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .progress-step.active .step-circle {
            background: linear-gradient(135deg, var(--primary-pink), var(--primary-blue));
            color: white;
            transform: scale(1.1);
        }

        .step-label {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--gray-600);
            transition: color 0.3s ease;
        }

        .progress-step.active .step-label {
            color: var(--primary-blue);
            font-weight: 600;
        }

        .progress-bar {
            width: 100%;
            background: var(--gray-200);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-pink), var(--primary-blue));
            transition: width 0.6s ease;
            border-radius: 4px;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            font-weight: 500;
            animation: slideInDown 0.5s ease-out;
            border-left: 4px solid;
        }

        .alert-success {
            background: var(--soft-pink);
            color: var(--primary-blue);
            border-left-color: var(--primary-pink);
        }

        .alert-error {
            background: #FEE2E2;
            color: #991B1B;
            border-left-color: #EF4444;
        }

        /* Main Form Card */
        .form-card {
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            animation: slideInUp 0.8s ease-out 0.4s both;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-blue) 100%);
            padding: 2rem;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .form-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .form-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .form-content {
            padding: 3rem;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0.75rem;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-pink);
            box-shadow: 0 0 0 3px rgba(216, 137, 227, 0.1);
            transform: translateY(-1px);
        }

        .form-input.error, .form-select.error {
            border-color: #EF4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .error-message {
            color: #EF4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
        }

        .error-message i {
            margin-right: 0.25rem;
        }

        /* Brand Selection Grid */
        .brand-selector {
            margin-bottom: 1.5rem;
        }

        .brand-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .brand-card {
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            padding: 1.5rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .brand-card:hover {
            border-color: var(--light-pink);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(216, 137, 227, 0.2);
        }

        .brand-card.selected {
            border-color: var(--primary-pink);
            background: var(--soft-pink);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(216, 137, 227, 0.3);
        }

        .brand-card img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-bottom: 0.5rem;
        }

        .brand-card span {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .brand-card.selected span {
            color: var(--primary-blue);
        }

        /* Buttons */
        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            background: linear-gradient(135deg, var(--primary-pink), var(--primary-blue));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(216, 137, 227, 0.4);
        }

        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 2px solid var(--gray-200);
        }

        .btn-secondary:hover {
            background: var(--gray-200);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 3rem;
            gap: 1rem;
        }

        /* Insurance Options */
        .insurance-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .insurance-card {
            background: var(--white);
            border: 2px solid var(--gray-200);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .insurance-card:hover {
            border-color: var(--primary-pink);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(216, 137, 227, 0.2);
        }

        .insurance-card.selected {
            border-color: var(--primary-pink);
            background: var(--soft-pink);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(216, 137, 227, 0.3);
        }

        .insurance-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }

        .insurance-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-pink);
            margin-bottom: 0.5rem;
        }

        .insurance-period {
            color: var(--gray-600);
            margin-bottom: 2rem;
        }

        .insurance-features {
            text-align: left;
            margin-bottom: 2rem;
        }

        .insurance-features li {
            margin-bottom: 0.75rem;
            display: flex;
            align-items: flex-start;
        }

        .insurance-features li i {
            color: var(--primary-pink);
            margin-right: 0.5rem;
            margin-top: 0.25rem;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
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

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-content {
                padding: 2rem;
            }

            .brand-grid {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
                gap: 0.75rem;
            }

            .brand-card {
                padding: 1rem 0.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .progress-steps {
                font-size: 0.875rem;
            }

            .step-circle {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 480px) {
            .insurance-options {
                grid-template-columns: 1fr;
            }

            .brand-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shield-alt"></i> Devis Assurance Auto</h1>
            <p class="subtitle">Neo Assurances - Votre protection sur mesure</p>
        </div>

        <!-- Success/Error Messages -->
        <div class="alert alert-success" style="display: none;">
            <i class="fas fa-check-circle"></i> Votre demande a été traitée avec succès
        </div>

        <div class="alert alert-error" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <ul style="margin-left: 1rem; margin-top: 0.5rem;">
                <li>Exemple d'erreur de validation</li>
            </ul>
        </div>

        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-steps">
                <div class="progress-step active">
                    <div class="step-circle">1</div>
                    <div class="step-label">Véhicule</div>
                </div>
                <div class="progress-step">
                    <div class="step-circle">2</div>
                    <div class="step-label">Conducteur</div>
                </div>
                <div class="progress-step">
                    <div class="step-circle">3</div>
                    <div class="step-label">Résultat</div>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 33.33%"></div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="form-card" x-data="formValidation()">
            <div class="form-header">
                <h2><i class="fas fa-car"></i> Informations du Véhicule</h2>
            </div>

            <div class="form-content">
                <form method="POST" action="#" @submit.prevent="submitForm">
                    <input type="hidden" name="step" value="1">

                    <div class="form-group">
                        <label class="form-label" for="vehicle_type">
                            <i class="fas fa-car-side"></i> Type de véhicule
                        </label>
                        <select name="vehicle_type"
                                id="vehicle_type"
                                x-model="vehicle_type"
                                @change="validateVehicleType()"
                                class="form-select"
                                :class="{ 'error': vehicleTypeError }">
                            <option value="" disabled>Sélectionner le type</option>
                            <option value="sedan">Berline</option>
                            <option value="suv">SUV</option>
                            <option value="truck">Camion</option>
                            <option value="motorcycle">Moto</option>
                        </select>
                        <div x-show="vehicleTypeError" class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            Le type de véhicule est requis.
                        </div>
                    </div>

                    <div class="form-group brand-selector" x-data="{ make: '' }">
                        <label class="form-label">
                            <i class="fas fa-industry"></i> Marque
                        </label>
                        <select x-model="make" class="form-select" style="margin-bottom: 1rem;">
                            <option value="">-- Sélectionnez --</option>
                            <option value="AUDI">Audi</option>
                            <option value="BMW">BMW</option>
                            <option value="CITROEN">Citroën</option>
                            <option value="DACIA">Dacia</option>
                            <option value="FIAT">Fiat</option>
                            <option value="HYUNDAI">Hyundai</option>
                            <option value="MERCEDES">Mercedes</option>
                            <option value="PEUGEOT">Peugeot</option>
                            <option value="RENAULT">Renault</option>
                            <option value="TOYOTA">Toyota</option>
                            <option value="VOLKSWAGEN">Volkswagen</option>
                        </select>

                        <div class="brand-grid">
                            <div class="brand-card"
                                 @click="make = 'AUDI'"
                                 :class="{ 'selected': make === 'AUDI' }">
                                <div style="width: 40px; height: 40px; background: #FF6B6B; border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">A</div>
                                <span>AUDI</span>
                            </div>

                            <div class="brand-card"
                                 @click="make = 'BMW'"
                                 :class="{ 'selected': make === 'BMW' }">
                                <div style="width: 40px; height: 40px; background: #4ECDC4; border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">B</div>
                                <span>BMW</span>
                            </div>

                            <div class="brand-card"
                                 @click="make = 'CITROEN'"
                                 :class="{ 'selected': make === 'CITROEN' }">
                                <div style="width: 40px; height: 40px; background: #45B7D1; border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">C</div>
                                <span>CITROËN</span>
                            </div>

                            <div class="brand-card"
                                 @click="make = 'DACIA'"
                                 :class="{ 'selected': make === 'DACIA' }">
                                <div style="width: 40px; height: 40px; background: #96CEB4; border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">D</div>
                                <span>DACIA</span>
                            </div>

                            <div class="brand-card"
                                 @click="make = 'MERCEDES'"
                                 :class="{ 'selected': make === 'MERCEDES' }">
                                <div style="width: 40px; height: 40px; background: #A8E6CF; border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">M</div>
                                <span>MERCEDES</span>
                            </div>

                            <div class="brand-card"
                                 @click="make = 'PEUGEOT'"
                                 :class="{ 'selected': make === 'PEUGEOT' }">
                                <div style="width: 40px; height: 40px; background: var(--primary-pink); border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">P</div>
                                <span>PEUGEOT</span>
                            </div>

                            <div class="brand-card"
                                 @click="make = 'RENAULT'"
                                 :class="{ 'selected': make === 'RENAULT' }">
                                <div style="width: 40px; height: 40px; background: var(--primary-blue); border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">R</div>
                                <span>RENAULT</span>
                            </div>

                            <div class="brand-card"
                                 @click="make = 'TOYOTA'"
                                 :class="{ 'selected': make === 'TOYOTA' }">
                                <div style="width: 40px; height: 40px; background: #FFD93D; border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">T</div>
                                <span>TOYOTA</span>
                            </div>
                        </div>

                        <input type="hidden" name="make" x-model="make">
                        <div x-show="makeError" class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            La marque est requise.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="model">
                            <i class="fas fa-tag"></i> Modèle
                        </label>
                        <input type="text"
                               name="model"
                               id="model"
                               x-model="model"
                               @input="validateModel()"
                               placeholder="Ex: 308, Clio, Serie 3..."
                               class="form-input"
                               :class="{ 'error': modelError }">
                        <div x-show="modelError" class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            Le modèle est requis.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="fuel_type">
                            <i class="fas fa-gas-pump"></i> Carburant
                        </label>
                        <select name="fuel_type"
                                id="fuel_type"
                                x-model="fuel_type"
                                @change="validateFuelType()"
                                class="form-select"
                                :class="{ 'error': fuelTypeError }">
                            <option value="" disabled>Sélectionner le carburant</option>
                            <option value="ESSENCE">Essence</option>
                            <option value="DIESEL">Diesel</option>
                            <option value="ELECTRIQUE">Électrique</option>
                            <option value="HYBRIDE">Hybride</option>
                        </select>
                        <div x-show="fuelTypeError" class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            Le type de carburant est requis.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tax_horsepower">
                            <i class="fas fa-tachometer-alt"></i> Puissance Fiscale (CV)
                        </label>
                        <input type="number"
                               name="tax_horsepower"
                               id="tax_horsepower"
                               x-model="tax_horsepower"
                               @input="validateTaxHorsepower()"
                               min="1"
                               placeholder="Ex: 6"
                               class="form-input"
                               :class="{ 'error': taxHorsepowerError }">
                        <div x-show="taxHorsepowerError" class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            La puissance fiscale doit être positive.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="vehicle_value">
                            <i class="fas fa-coins"></i> Valeur du Véhicule (DH)
                        </label>
                        <input type="number"
                               name="vehicle_value"
                               id="vehicle_value"
                               x-model="vehicle_value"
                               @input="validateVehicleValue()"
                               min="1000"
                               placeholder="Ex: 150000"
                               class="form-input"
                               :class="{ 'error': vehicleValueError }">
                        <div x-show="vehicleValueError" class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            La valeur doit être supérieure ou égale à 1000 DH.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="registration_date">
                            <i class="fas fa-calendar-alt"></i> Date de Mise en Circulation
                        </label>
                        <input type="date"
                               name="registration_date"
                               id="registration_date"
                               x-model="registration_date"
                               @input="validateRegistrationDate()"
                               class="form-input"
                               :class="{ 'error': registrationDateError }">
                        <div x-show="registrationDateError" class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            La date doit être antérieure à aujourd'hui.
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="#" class="btn btn-secondary">
                            <i class="fas fa-refresh"></i> Réinitialiser
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Suivant <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Insurance Options Demo -->
        <div class="insurance-options" style="margin-top: 3rem;">
            <div class="insurance-card">
                <h3>Basique</h3>
                <div class="insurance-price">2,450 DH</div>
                <div class="insurance-period">par an</div>
                <ul class="insurance-features">
                    <li><i
