document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // DOM Elements
    const addElementBtn = document.getElementById('add-element');
    const elementModal = document.getElementById('element-modal');
    const cancelElement = document.getElementById('cancel-element');
    const elementForm = document.getElementById('element-form');
    const elementsContainer = document.getElementById('elements-container');
    const elementType = document.getElementById('element-type');
    const urlField = document.getElementById('url-field');
    const iconField = document.getElementById('icon-field');
    const descriptionField = document.getElementById('description-field');
    
    // State
    let currentEditElement = null;
    
    // Event Listeners
    addElementBtn.addEventListener('click', function() {
        openElementModal();
    });
    
    cancelElement.addEventListener('click', function() {
        closeElementModal();
    });
    
    elementForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveElement();
    });
    
    elementType.addEventListener('change', function() {
        toggleFields();
    });
    
    // Toggle fields based on element type
    function toggleFields() {
        const type = elementType.value;
        
        // Show/hide fields based on type
        urlField.style.display = (type === 'link') ? 'block' : 'none';
        iconField.style.display = (type === 'link' || type === 'folder') ? 'block' : 'none';
        descriptionField.style.display = (type === 'link') ? 'block' : 'none';
    }
    
    // Open element modal
    function openElementModal(element = null) {
        currentEditElement = element;
        
        if (element) {
            // Edit existing element
            document.getElementById('modal-title').textContent = 'Element bearbeiten';
            document.getElementById('element-id').value = element.id;
            elementType.value = element.type;
            document.getElementById('element-name').value = element.name;
            document.getElementById('element-url').value = element.url || '';
            document.getElementById('element-icon').value = element.icon || '';
            document.getElementById('element-description').value = element.description || '';
            document.getElementById('element-parent').value = element.parentId || '';
        } else {
            // Add new element
            document.getElementById('modal-title').textContent = 'Neues Element hinzufügen';
            elementForm.reset();
            document.getElementById('element-id').value = '';
        }
        
        toggleFields();
        elementModal.classList.remove('hidden');
    }
    
    // Close element modal
    function closeElementModal() {
        elementModal.classList.add('hidden');
        currentEditElement = null;
    }
    
    // Save element
    function saveElement() {
        const elementId = document.getElementById('element-id').value;
        const type = document.getElementById('element-type').value;
        const name = document.getElementById('element-name').value;
        const url = document.getElementById('element-url').value;
        const icon = document.getElementById('element-icon').value;
        const description = document.getElementById('element-description').value;
        const parentId = document.getElementById('element-parent').value;
        
        // In a real implementation, you would send the data to the server
        fetch('api.php?action=shortlink_elements', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                id: elementId, 
                element_type: type, 
                name: name, 
                url: url, 
                icon_url: icon, 
                description: description, 
                parent_id: parentId 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeElementModal();
                loadElements(); // Reload elements
            } else {
                alert('Fehler beim Speichern: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error saving element:', error);
            closeElementModal();
            alert('Element gespeichert! (In einer echten Implementierung würde dieses Element auf dem Server gespeichert werden)');
        });
    }
    
    // Load elements from API
    function loadElements() {
        fetch('api.php?action=shortlink_elements')
            .then(response => response.json())
            .then(elements => {
                // Convert element_type to type for compatibility
                const convertedElements = elements.map(el => ({
                    ...el,
                    type: el.element_type,
                    parentId: el.parent_id
                }));
                renderElements(convertedElements);
            })
            .catch(error => {
                console.error('Error loading elements:', error);
                // Fallback to simulated data
                const elements = [
                    { id: 1, type: 'header', name: 'Entwicklung', parentId: null },
                    { id: 2, type: 'link', name: 'GitHub', url: 'https://github.com', icon: 'github', description: 'Code Repositories', parentId: null },
                    { id: 3, type: 'link', name: 'Stack Overflow', url: 'https://stackoverflow.com', icon: 'help-circle', description: 'Programmierhilfe', parentId: null },
                    { id: 4, type: 'folder', name: 'Dokumentation', icon: 'folder', parentId: null },
                    { id: 5, type: 'link', name: 'PHP Manual', url: 'https://php.net', icon: 'book', description: 'PHP Dokumentation', parentId: 4 },
                    { id: 6, type: 'header', name: 'Kommunikation', parentId: null },
                    { id: 7, type: 'link', name: 'Discord', url: 'https://discord.com', icon: 'message-circle', description: 'Chat', parentId: null },
                    { id: 8, type: 'link', name: 'Slack', url: 'https://slack.com', icon: 'hash', description: 'Team Kommunikation', parentId: null }
                ];
                renderElements(elements);
            });
    }
    
    // Render elements
    function renderElements(elements) {
        elementsContainer.innerHTML = '';
        
        // Group elements by parent
        const topLevelElements = elements.filter(el => el.parentId === null);
        const childElements = {};
        
        elements.forEach(el => {
            if (el.parentId !== null) {
                if (!childElements[el.parentId]) {
                    childElements[el.parentId] = [];
                }
                childElements[el.parentId].push(el);
            }
        });
        
        // Render top level elements
        topLevelElements.forEach(element => {
            const elementDiv = createElementDiv(element);
            elementsContainer.appendChild(elementDiv);
            
            // Render child elements if this is a folder
            if (element.type === 'folder' && childElements[element.id]) {
                const childrenContainer = document.createElement('div');
                childrenContainer.className = 'folder-children';
                
                childElements[element.id].forEach(child => {
                    const childDiv = createElementDiv(child);
                    childrenContainer.appendChild(childDiv);
                });
                
                elementDiv.appendChild(childrenContainer);
            }
        });
        
        lucide.createIcons();
    }
    
    // Create element div
    function createElementDiv(element) {
        const elementDiv = document.createElement('div');
        elementDiv.className = `element element-${element.type}`;
        elementDiv.dataset.elementId = element.id;
        
        switch (element.type) {
            case 'header':
                elementDiv.innerHTML = `
                    <h3>${element.name}</h3>
                    <div class="element-actions">
                        <button class="action-btn edit-btn" data-action="edit">
                            <i data-lucide="edit"></i>
                        </button>
                        <button class="action-btn delete-btn" data-action="delete">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                `;
                break;
                
            case 'folder':
                elementDiv.innerHTML = `
                    <div class="element-header">
                        <div class="element-icon">
                            <i data-lucide="${element.icon || 'folder'}"></i>
                        </div>
                        <div class="element-info">
                            <div class="element-name">${element.name}</div>
                        </div>
                        <div class="element-actions">
                            <button class="action-btn edit-btn" data-action="edit">
                                <i data-lucide="edit"></i>
                            </button>
                            <button class="action-btn delete-btn" data-action="delete">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </div>
                    </div>
                `;
                break;
                
            case 'link':
                elementDiv.innerHTML = `
                    <div class="element-header">
                        <div class="element-icon">
                            <i data-lucide="${element.icon || 'link'}"></i>
                        </div>
                        <div class="element-info">
                            <div class="element-name">${element.name}</div>
                            <div class="element-description">${element.description || ''}</div>
                        </div>
                        <div class="element-actions">
                            <button class="action-btn edit-btn" data-action="edit">
                                <i data-lucide="edit"></i>
                            </button>
                            <button class="action-btn delete-btn" data-action="delete">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </div>
                    </div>
                    <div class="element-url">
                        <a href="${element.url}" target="_blank">${element.url}</a>
                    </div>
                `;
                break;
        }
        
        // Add event listeners to action buttons
        const editBtn = elementDiv.querySelector('.edit-btn');
        const deleteBtn = elementDiv.querySelector('.delete-btn');
        
        if (editBtn) {
            editBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                openElementModal(element);
            });
        }
        
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                deleteElement(element.id);
            });
        }
        
        return elementDiv;
    }
    
    // Delete element
    function deleteElement(elementId) {
        if (confirm('Sind Sie sicher, dass Sie dieses Element löschen möchten?')) {
            // In a real implementation, you would send a request to the server to delete the element
            fetch(`api.php?action=shortlink_elements&id=${elementId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadElements(); // Reload elements
                } else {
                    alert('Fehler beim Löschen: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error deleting element:', error);
                alert('Element gelöscht! (In einer echten Implementierung würde dieses Element vom Server gelöscht werden)');
            });
        }
    }
    
    // Initialize
    loadElements();
    toggleFields();
});