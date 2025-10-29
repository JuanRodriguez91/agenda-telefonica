class ContactManager {
  constructor() {
    this.contacts = [];
    this.isEditing = false;
    this.currentEditId = null;
    this.apiUrl = '../api-v2/public/contacts';
    this.isLogged = false;

    this.initLoginObserver();
    this.updateLoginState();
  }

  initLoginObserver() {
    const observer = new MutationObserver(() => this.updateLoginState());
    observer.observe(document.body, { attributes: true, attributeFilter: ['data-is-logged'] });
  }

  updateLoginState() {
    const logged = document.body.dataset.isLogged === 'true';
    if (logged === this.isLogged) return; // No ha cambiado
    this.isLogged = logged;

    if (this.isLogged) {
      console.log('Usuario logueado: inicializando ContactManager...');
      this.init();
      this.enableUI();
    } else {
      console.warn('Usuario no logueado: ContactManager deshabilitado.');
      this.disableUI();
    }
  }

  enableUI() {
    // Habilitar botones y inputs si es necesario
    document.getElementById('addContactBtn').disabled = false;
    document.getElementById('searchInput').disabled = false;
    // Cargar contactos si no se habían cargado antes
    if (!this.contacts.length) this.loadContacts();
  }

  disableUI() {
    // Cerrar modal si está abierto
    this.closeModal();
    // Vaciar lista y mostrar mensaje
    document.getElementById('contactsList').innerHTML = '';
    document.getElementById('emptyState').style.display = 'block';
    // Deshabilitar botones e inputs
    document.getElementById('addContactBtn').disabled = true;
    document.getElementById('searchInput').disabled = true;
  }

  init() {
    this.loadContacts();
    this.setupEventListeners();
  }

  setupEventListeners() {
    document.getElementById('addContactBtn').addEventListener('click', () => {
      if (!this.isLogged) {
        return;
      }
      this.openModal();
    });

    document.getElementById('closeModalBtn').addEventListener('click', () => {
      this.closeModal();
    });

    document.getElementById('cancelBtn').addEventListener('click', () => {
      this.closeModal();
    });

    document.getElementById('contactForm').addEventListener('submit', (e) => {
      e.preventDefault();
      if (!this.isLogged) {
        return;
      }
      this.handleFormSubmit();
    });

    document.getElementById('searchInput').addEventListener('input', (e) => {
      this.filterContacts(e.target.value);
    });

    document.getElementById('contactModal').addEventListener('click', (e) => {
      if (e.target.id === 'contactModal') {
        this.closeModal();
      }
    });
  }

  async loadContacts() {
    if (!this.isLogged) {
      return;
    }

    try {
      const response = await fetch(this.apiUrl);
      if (!response.ok) {
        throw new Error('Error al cargar contactos');
      }
      this.contacts = await response.json();
      this.renderContacts();
    } catch (error) {
      console.error('Error:', error);
      this.showNotification('Error al cargar contactos', 'error');
    }
  }

  async handleFormSubmit() {
    if (!this.isLogged) {
      return;
    }

    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const email = document.getElementById('email').value.trim();

    if (!name || !phone || !email) {
      this.showNotification('Por favor, complete todos los campos', 'error');
      return;
    }

    if (!this.validateEmail(email)) {
      this.showNotification('Por favor, ingrese un correo electrónico válido', 'error');
      return;
    }

    const contactData = { name, phone, email };

    try {
      if (this.isEditing) {
        await this.updateContact(this.currentEditId, contactData);
      } else {
        await this.addContact(contactData);
      }
    } catch (error) {
      console.error('Error:', error);
      this.showNotification('Error al guardar el contacto: ' + error.message, 'error');
    }
  }

  async addContact(contact) {
    if (!this.isLogged) {
      return;
    }

    const response = await fetch(this.apiUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(contact)
    });

    const responseData = await response.json();

    if (!response.ok) {
      throw new Error(responseData.message);
    }

    this.contacts.push(responseData);
    this.renderContacts();
    this.closeModal();
    this.showNotification('Contacto agregado', 'success');
  }

  async updateContact(id, updatedContact) {
    if (!this.isLogged) {
      return;
    }

    const response = await fetch(`${this.apiUrl}/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(updatedContact)
    });

    const responseData = await response.json();
    if (!response.ok) {
      throw new Error(responseData.message);
    }

    const index = this.contacts.findIndex(contact => contact.id === parseInt(id));
    if (index !== -1) {
      this.contacts[index] = responseData;
    }

    this.renderContacts();
    this.closeModal();
    this.showNotification('Contacto actualizado', 'success');
  }

  async deleteContact(id, name) {
    if (!this.isLogged) {
      return;
    }

    const confirmed = await window.confirmModal.open(
      `¿Está seguro de que desea eliminar el contacto <strong>${name}</strong>?`,
      'Sí, eliminar',
      'Cancelar'
    );

    if (!confirmed) {
      return;
    }

    try {
      const response = await fetch(`${this.apiUrl}/${id}`, {
        method: 'DELETE'
      });

      if (!response.ok) {
        throw new Error('Error al eliminar contacto');
      }

      this.contacts = this.contacts.filter(contact => contact.id !== parseInt(id));
      this.renderContacts();
      this.showNotification('Contacto eliminado', 'success');
    } catch (error) {
      console.error('Error:', error);
      this.showNotification('Error al eliminar el contacto', 'error');
    }
  }

  editContact(id) {
    if (!this.isLogged) {
      return;
    }

    const contact = this.contacts.find(contact => contact.id === parseInt(id));
    if (!contact) {
      return;
    }

    document.getElementById('contactId').value = contact.id;
    document.getElementById('name').value = contact.name;
    document.getElementById('phone').value = contact.phone;
    document.getElementById('email').value = contact.email;

    document.getElementById('modalTitle').textContent = 'Editar Contacto';
    document.getElementById('submitBtn').textContent = 'Actualizar Contacto';

    this.isEditing = true;
    this.currentEditId = id;
    this.openModal();
  }

  openModal() {
    document.getElementById('contactModal').classList.add('active');
  }

  closeModal() {
    document.getElementById('contactModal').classList.remove('active');
    this.resetForm();
  }

  resetForm() {
    document.getElementById('contactForm').reset();
    document.getElementById('contactId').value = '';
    document.getElementById('modalTitle').textContent = 'Añadir Contacto';
    document.getElementById('submitBtn').textContent = 'Añadir Contacto';

    this.isEditing = false;
    this.currentEditId = null;
  }

  filterContacts(searchTerm) {
    const filteredContacts = this.contacts.filter(contact =>
      contact.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      contact.phone.includes(searchTerm) ||
      contact.email.toLowerCase().includes(searchTerm.toLowerCase())
    );

    this.renderContacts(filteredContacts);
  }

  renderContacts(contactsToRender = null) {
    const contacts = contactsToRender || this.contacts;
    const contactsList = document.getElementById('contactsList');
    const emptyState = document.getElementById('emptyState');

    if (contacts.length === 0) {
      contactsList.innerHTML = '';
      emptyState.style.display = 'block';
      return;
    }

    emptyState.style.display = 'none';

    contactsList.innerHTML = contacts.map(contact => `
            <div class="contact-card">
                <div class="contact-avatar">
                    ${contact.name.charAt(0).toUpperCase()}
                </div>
                <div class="contact-info">
                    <div class="contact-name">${this.escapeHtml(contact.name)}</div>
                    <div class="contact-details">
                        <div class="contact-phone">
                            <i class="fas fa-phone-alt me-2"></i> ${this.escapeHtml(contact.phone)}
                        </div>
                        <div class="contact-email">
                            <i class="fas fa-envelope me-2"></i> ${this.escapeHtml(contact.email)}
                        </div>
                    </div>
                </div>
                <div class="contact-actions">
                    <button class="btn-action btn-edit" onclick="contactManager.editContact('${contact.id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action btn-delete" onclick="contactManager.deleteContact('${contact.id}', '${contact.name}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
  }

  validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  showNotification(message, type) {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type} show`;

    setTimeout(() => {
      notification.classList.remove('show');
    }, 3000);
  }

  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
}

// Inicializar la aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  window.contactManager = new ContactManager();
});