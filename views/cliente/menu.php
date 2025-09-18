<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">
            <i class="fa-solid fa-mug-hot"></i> SABORES Y MOMENTOS (Menú)
        </h1>
        <p class="text-muted">Explora nuestra selección y ordena lo que más te guste</p>
    </div>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php while ($producto = $productos->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 rounded-3 product-card" 
                 data-url="<?php echo BASE_URL; ?>cliente/pedido">
                <img src="<?php echo BASE_URL; ?>img/<?php echo $producto['id']; ?>.jpg"
                     class="card-img-top rounded-top" 
                     alt="<?php echo $producto['nombre']; ?>"
                     style="height: 220px; object-fit: cover;">
                <div class="card-body d-flex flex-column text-center">
                    <h5 class="card-title fw-bold mb-2"><?php echo $producto['nombre']; ?></h5>
                    <p class="text-muted mb-1"><?php echo $producto['categoria']; ?></p>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
.product-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            // Redirigir al hacer clic en cualquier parte de la tarjeta
            const url = this.dataset.url;
            if (url) {
                window.location.href = url;
            }
        });
    });
});
</script>
