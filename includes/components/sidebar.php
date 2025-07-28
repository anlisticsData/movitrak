<style> 
 .sidebar {
    min-height: 100vh; /* ocupa altura toda no desktop */
}
</style>

<!-- Botão Hamburguer só aparece no mobile -->
<button class="btn btn-primary d-md-none" type="button" data-toggle="collapse" data-target="#mobileSidebar">
    <i class="fas fa-bars"></i> Menu
</button>

<!-- Sidebar que colapsa no mobile -->
<nav class="col-md-2 bg-light sidebar collapse d-md-block" id="mobileSidebar">
    <div class="sidebar-sticky p-2">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="../home/dashboard">
                    <i class="fas fa-tachometer-alt"></i> Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../user/profile">
                    <i class="fas fa-user"></i> Perfil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../cameras/cameras">
                    <i class="fas fa-camera"></i> Câmeras
                </a>
            </li>
        </ul>
    </div>
</nav>
