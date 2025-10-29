class ConfirmModal {
  constructor() {
    this.modal = null;
    this.messageElement = null;
    this.confirmYesBtn = null;
    this.confirmNoBtn = null;
    this.closeBtn = null;

    this.resolvePromise = null;
    this.rejectPromise = null;

    this.init();
  }

  init() {
    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => this.setup());
    } else {
      this.setup();
    }
  }

  setup() {
    this.modal = document.getElementById('confirmModal');
    this.messageElement = document.getElementById('confirmMessage');
    this.confirmYesBtn = document.getElementById('confirmYesBtn');
    this.confirmNoBtn = document.getElementById('confirmNoBtn');
    this.closeBtn = document.getElementById('closeConfirmModalBtn');

    if (!this.modal || !this.messageElement || !this.confirmYesBtn || !this.confirmNoBtn || !this.closeBtn) {
      console.error('No se pudieron encontrar todos los elementos del modal de confirmación');
      return;
    }

    this.setupEventListeners();
  }

  setupEventListeners() {
    this.confirmYesBtn.addEventListener('click', () => {
      this.resolve(true);
      this.close();
    });

    this.confirmNoBtn.addEventListener('click', () => {
      this.resolve(false);
      this.close();
    });

    this.closeBtn.addEventListener('click', () => {
      this.resolve(false);
      this.close();
    });

    // Cerrar al hacer clic fuera del modal
    this.modal.addEventListener('click', (e) => {
      if (e.target === this.modal) {
        this.resolve(false);
        this.close();
      }
    });

    // Cerrar con tecla Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.modal.classList.contains('active')) {
        this.resolve(false);
        this.close();
      }
    });
  }

  open(message = '¿Está seguro de que desea realizar esta acción?', btnYes = 'Yes', btnNo = 'No') {
    this.messageElement.innerHTML = message;
    this.confirmYesBtn.innerHTML = btnYes;
    this.confirmNoBtn.innerHTML = btnNo;
    this.modal.classList.add('active');

    return new Promise((resolve, reject) => {
      this.resolvePromise = resolve;
      this.rejectPromise = reject;
    });
  }

  close() {
    this.modal.classList.remove('active');
  }

  resolve(value) {
    if (this.resolvePromise) {
      this.resolvePromise(value);
      this.resolvePromise = null;
      this.rejectPromise = null;
    }
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  window.confirmModal = new ConfirmModal();
});