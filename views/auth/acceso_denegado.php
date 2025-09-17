<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - Cafetería Nova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            text-align: center;
            padding: 20px;
        }
        .btn-primary {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #664d03;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Acceso Denegado</h3>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Cuenta no activada</strong>
                            <p class="mt-2 mb-0">Su cuenta está pendiente de activación por un administrador.</p>
                        </div>
                        
                        <div class="my-4">
                            <i class="fas fa-user-clock fa-4x text-secondary"></i>
                        </div>
                        
                        <p>Por favor, espere a que un administrador active su cuenta.</p>
                        
                        <a href="<?php echo BASE_URL; ?>auth/login" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Login
                        </a>
                    </div>
                </div>
                
                <div class="text-center text-white mt-4">
                    <p>© 2023 Cafetería Nova. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>