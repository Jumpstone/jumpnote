document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // DOM Elements
    const editToggle = document.getElementById('edit-toggle');
    const addLinkBtn = document.getElementById('add-link-btn');
    const editOverlay = document.getElementById('edit-overlay');
    const cancelEdit = document.getElementById('cancel-edit');
    const editLinkForm = document.getElementById('edit-link-form');
    const linksContainer = document.getElementById('links-container');
    
    // State
    let isEditMode = false;
    let currentEditLink = null;
    
    // Toggle edit mode
    editToggle.addEventListener('click', function() {
        isEditMode = !isEditMode;
        document.body.classList.toggle('edit-mode', isEditMode);
        
        if (isEditMode) {
            editToggle.innerHTML = '<i data-lucide="check"></i> Fertig';
            enableEditFeatures();
        } else {
            editToggle.innerHTML = '<i data-lucide="edit"></i> Bearbeiten';
            disableEditFeatures();
        }
        
        lucide.createIcons();
    });
    
    // Add new link
    addLinkBtn.addEventListener('click', function() {
        currentEditLink = null; // Indicates we're adding a new link
        
        // Clear form values
        document.getElementById('edit-link-id').value = '';
        document.getElementById('edit-link-name').value = '';
        document.getElementById('edit-link-url').value = '';
        document.getElementById('edit-link-icon').value = '';
        
        // Change modal title
        document.querySelector('#edit-overlay .edit-modal h3').textContent = 'Neuen Link hinzufügen';
        
        editOverlay.classList.remove('hidden');
        lucide.createIcons();
    });
    
    // Cancel edit
    cancelEdit.addEventListener('click', function() {
        editOverlay.classList.add('hidden');
        // Reset modal title
        document.querySelector('#edit-overlay .edit-modal h3').textContent = 'Link bearbeiten';
    });
    
    // Handle form submission
    editLinkForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveLinkChanges();
    });
    
    // Enable edit features
    function enableEditFeatures() {
        // Add wobble effect to link cards
        const linkCards = document.querySelectorAll('.link-card');
        linkCards.forEach(card => {
            card.classList.add('edit-mode');
            
            // Add click event to edit links
            card.addEventListener('click', function(e) {
                if (e.target.closest('.link-card')) {
                    const linkId = this.dataset.linkId;
                    openEditModal(linkId);
                }
            });
        });
    }
    
    // Disable edit features
    function disableEditFeatures() {
        // Remove wobble effect from link cards
        const linkCards = document.querySelectorAll('.link-card');
        linkCards.forEach(card => {
            card.classList.remove('edit-mode');
        });
    }
    
    // Open edit modal
    function openEditModal(linkId) {
        // In a real implementation, you would fetch the link data from the server
        // For now, we'll just simulate it
        currentEditLink = linkId;
        
        // Change modal title
        document.querySelector('#edit-overlay .edit-modal h3').textContent = 'Link bearbeiten';
        
        // Set form values (simulated)
        document.getElementById('edit-link-id').value = linkId;
        document.getElementById('edit-link-name').value = 'Example Link';
        document.getElementById('edit-link-url').value = 'https://example.com';
        document.getElementById('edit-link-icon').value = '';
        
        editOverlay.classList.remove('hidden');
    }
    
    // Save link changes
    function saveLinkChanges() {
        const linkId = document.getElementById('edit-link-id').value;
        const name = document.getElementById('edit-link-name').value;
        const url = document.getElementById('edit-link-url').value;
        const icon = document.getElementById('edit-link-icon').value;
        
        // Validation
        if (!name || !url) {
            alert('Bitte füllen Sie alle Pflichtfelder aus.');
            return;
        }
        
        // Validate URL format
        try {
            new URL(url);
        } catch (e) {
            alert('Bitte geben Sie eine gültige URL ein.');
            return;
        }
        
        // Prepare data
        const data = {
            name: name,
            url: url,
            icon_url: icon || ''
        };
        
        // Determine if we're creating or updating
        let method = 'POST';
        let action = 'links';
        if (linkId) {
            // Updating existing link
            data.id = linkId;
            method = 'PUT';
        }
        
        // Send data to server
        fetch('api.php?action=' + action, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                editOverlay.classList.add('hidden');
                loadLinks(); // Reload links
            } else {
                alert('Fehler beim Speichern: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error saving link:', error);
            editOverlay.classList.add('hidden');
            // For demo purposes, still reload links even if server error
            loadLinks();
            alert('Änderungen gespeichert! (In einer echten Implementierung würden diese Änderungen auf dem Server gespeichert werden)');
        });
    }
    
    // Load links from API
    function loadLinks() {
        fetch('api.php?action=links')
            .then(response => response.json())
            .then(links => {
                renderLinks(links);
            })
            .catch(error => {
                console.error('Error loading links:', error);
                // Fallback to simulated data
                const links = [
                    { id: 1, name: 'GitHub', url: 'https://github.com', icon: 'github' },
                    { id: 2, name: 'Gmail', url: 'https://gmail.com', icon: 'mail' },
                    { id: 3, name: 'Calendar', url: 'https://calendar.google.com', icon: 'calendar' },
                    { id: 4, name: 'Drive', url: 'https://drive.google.com', icon: 'hard-drive' },
                    { id: 5, name: 'Docs', url: 'https://docs.google.com', icon: 'file-text' }
                ];
                renderLinks(links);
            });
    }
    
    // Widget framework
    function initWidgets() {
        // In a real implementation, you would initialize widgets here
        console.log('Widget framework initialized');
        
        // Example widget registration
        registerWidget('weather', {
            name: 'Wetter',
            render: function(container) {
                container.innerHTML = `
                    <h3>Wetter</h3>
                    <p>22°C, sonnig</p>
                    <p>Köln, Deutschland</p>
                `;
            }
        });
        
        registerWidget('calendar', {
            name: 'Kalender',
            render: function(container) {
                container.innerHTML = `
                    <h3>Kalender</h3>
                    <ul>
                        <li>Meeting - 14:00</li>
                        <li>Projekt Abgabe - 16:00</li>
                    </ul>
                `;
            }
        });
        
        // Render widgets
        renderWidgets();
    }
    
    // Widget registry
    const widgetRegistry = {};
    
    // Register a widget
    function registerWidget(id, widget) {
        widgetRegistry[id] = widget;
    }
    
    // Render widgets
    function renderWidgets() {
        // In a real implementation, you would fetch widget configuration from the server
        const widgetConfig = [
            { id: 'weather', type: 'weather' },
            { id: 'calendar', type: 'calendar' }
        ];
        
        const widgetsContainer = document.querySelector('.widgets-container');
        widgetsContainer.innerHTML = '';
        
        widgetConfig.forEach(config => {
            if (widgetRegistry[config.type]) {
                const widgetContainer = document.createElement('div');
                widgetContainer.className = 'widget';
                widgetsContainer.appendChild(widgetContainer);
                
                widgetRegistry[config.type].render(widgetContainer);
            }
        });
    }
    
    // Render links
    function renderLinks(links) {
        linksContainer.innerHTML = '';
        
        links.forEach(link => {
            const linkCard = document.createElement('div');
            linkCard.className = 'link-card';
            linkCard.dataset.linkId = link.id;
            
            linkCard.innerHTML = `
                <div class="link-icon">
                    <i data-lucide="${link.icon || 'link'}"></i>
                </div>
                <div class="link-name">${link.name}</div>
            `;
            
            linksContainer.appendChild(linkCard);
        });
        
        // Re-enable edit features if in edit mode
        if (isEditMode) {
            enableEditFeatures();
        }
        
        lucide.createIcons();
    }
    
    // Initialize
    loadLinks();
    initWidgets();
});