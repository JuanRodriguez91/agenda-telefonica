class UserAuth {
  constructor() {
    this.apiUrl = '../api-v2/public';

    // Elementos del DOM
    this.loginPage = document.querySelector('.login-page');
    this.appContainer = document.querySelector('.app-container');
    this.userCircle = document.querySelector('.user-circle');
    this.loginForm = document.getElementById('loginForm');
    this.loginEmail = document.getElementById('loginEmail');
    this.loginPassword = document.getElementById('loginPassword');
    this.loginErrorMsg = document.getElementById('loginErrorMsg');
    this.btnSubmit = this.loginForm?.querySelector('.btn-submit');
    this.btnLogout = document.getElementById('btnLogout');

    // Bind de eventos
    this.addEventListeners();

    // Chequear sesión al cargar
    this.checkSession();
  }

  addEventListeners() {
    if (this.loginForm) {
      this.loginForm.addEventListener('submit', (e) => this.handleLogin(e));
    }
    if (this.btnLogout) {
      this.btnLogout.addEventListener('click', () => this.logout());
    }
  }

  async handleLogin(e) {
    e.preventDefault();

    // Limpiar mensaje previo
    this.setError('');

    // Deshabilitar botón mientras carga
    this.btnSubmit.disabled = true;
    this.btnSubmit.textContent = 'Iniciando...';

    const email = this.loginEmail.value.trim();
    const password = this.loginPassword.value.trim();

    try {
      const response = await fetch(this.apiUrl + '/user/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (response.ok) {
        // Login correcto
        this.showApp(data);
      } else {
        // Error de login
        this.setError(data.message || 'Usuario y/o contraseña incorrecto');
      }
    } catch (error) {
      console.error('Error de conexión:', error);
      this.setError('Error de conexión con el servidor');
    } finally {
      // Restaurar botón
      this.btnSubmit.disabled = false;
      this.btnSubmit.textContent = 'Iniciar Sesión';
    }
  }

  async logout() {
    const confirmed = await window.confirmModal.open(`¿Estás seguro/a que quieres cerrar sesión?`);
    if (!confirmed) {
      return;
    }

    try {
      await fetch(this.apiUrl + '/user/logout');
    } catch (error) {
      console.warn('Error al cerrar sesión:', error);
    }
    this.showLogin();
  }

  async checkSession() {
    try {
      const response = await fetch(this.apiUrl + '/user/get-logged');
      const data = await response.json();

      if (response.ok && data?.email) {
        this.showApp(data);
      } else {
        this.showLogin();
      }
    } catch (error) {
      console.warn('Error al verificar sesión:', error);
      this.showLogin();
    }
  }

  showApp(userData) {
    // Mostrar app y ocultar login
    this.appContainer.style.display = 'block';
    this.loginPage.style.display = 'none';

    // Mostrar iniciales del usuario
    this.setUserCircle(userData.name || userData.email);
  }

  showLogin() {
    this.appContainer.style.display = 'none';
    this.loginPage.style.display = 'block';
    this.loginForm.reset();
    this.setError('');
  }

  setUserCircle(name) {
    const initials = name
      .trim()
      .substring(0, 2)
      .toUpperCase();
    this.userCircle.textContent = initials;
  }

  setError(message) {
    this.loginErrorMsg.textContent = message;
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => new UserAuth());
