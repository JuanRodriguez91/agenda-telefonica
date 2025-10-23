<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Telefónica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-container">
    <header class="app-header">
        <h1 class="app-title">
            <i class="fa-solid fa-address-book"></i>
            Agenda Telefónica
        </h1>
    </header>

    <div class="contacts-container">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" id="searchInput" placeholder="Buscar contactos...">
        </div>

        <div id="contactsList">
            <!-- Los contactos se cargarán aquí dinámicamente -->
        </div>

        <div id="emptyState" class="empty-state">
            <i class="fas fa-address-book"></i>
            <h3>No hay contactos</h3>
            <p>Agrega tu primer contacto haciendo clic en el botón +</p>
        </div>
    </div>

    <button class="floating-btn" id="addContactBtn">
        <i class="fas fa-plus"></i>
    </button>
</div>

<!-- Modal para añadir/editar contactos -->
<div class="modal-overlay" id="contactModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Añadir Contacto</h2>
            <button class="close-btn" id="closeModalBtn">&times;</button>
        </div>
        <form id="contactForm">
            <input type="hidden" id="contactId">
            <div class="form-group">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="name" maxlength="255" required>
            </div>
            <div class="form-group">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="phone" maxlength="50" pattern="[\d\s\+\-\(\)\.]{6,}"
                       title="El teléfono debe contener al menos 6 caracteres y solo puede incluir números, espacios, y los caracteres especiales: + - ( ) ."
                       required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" maxlength="255" required>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn-cancel flex-fill" id="cancelBtn">Cancelar</button>
                <button type="submit" class="btn-submit flex-fill" id="submitBtn">Añadir Contacto</button>
            </div>
        </form>
    </div>
</div>

<!-- Notificación -->
<div class="notification" id="notification"></div>

<!-- Modal de confirmación para eliminar -->
<div class="modal-overlay" id="confirmModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Confirmar Eliminación</h2>
            <button class="close-btn" id="closeConfirmModalBtn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="text-center py-3">
                <i class="fas fa-exclamation-triangle text-warning big-icon"></i>
                <p class="mt-3" id="confirmMessage">¿Está seguro de que desea eliminar este contacto?</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn-cancel flex-fill" id="cancelConfirmBtn">Cancelar</button>
                <button class="btn-submit flex-fill" id="confirmDeleteBtn">Sí, eliminar</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/confirm-modal.js"></script>
<script src="assets/js/contact-manager.js"></script>
</body>
</html>