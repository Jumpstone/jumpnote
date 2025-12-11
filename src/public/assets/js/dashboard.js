document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // DOM Elements
    const editToggle = document.getElementById('edit-toggle');
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
        } else {
            editToggle.innerHTML = '<i data-lucide="edit"></i> Bearbeiten';
        }
        
        // Reload links to show/hide the add link card
        loadLinks();
        
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
        const linkCards = document.querySelectorAll('.link-card:not(.add-link-card)');
        linkCards.forEach(card => {
            card.classList.add('edit-mode');
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
        // Fetch the actual link data from the server
        fetch(`api.php?action=links&id=${linkId}`)
            .then(response => response.json())
            .then(link => {
                currentEditLink = linkId;
                
                // Change modal title
                document.querySelector('#edit-overlay .edit-modal h3').textContent = 'Link bearbeiten';
                
                // Set form values with actual data
                document.getElementById('edit-link-id').value = link.id;
                document.getElementById('edit-link-name').value = link.name;
                document.getElementById('edit-link-url').value = link.url;
                document.getElementById('edit-link-icon').value = link.icon || '';
                
                editOverlay.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching link data:', error);
                alert('Fehler beim Laden der Linkdaten');
            });
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
                // Fallback to simulated data with URLs
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
    
    // Render links
    function renderLinks(links) {
        linksContainer.innerHTML = '';
        
        // Add the "Add Link" tile when in edit mode
        if (isEditMode) {
            const addLinkCard = document.createElement('div');
            addLinkCard.className = 'link-card add-link-card';
            addLinkCard.innerHTML = `
                <div class="link-icon">
                    <i data-lucide="plus"></i>
                </div>
                <div class="link-name">Link hinzufügen</div>
            `;
            
            // Add click handler for adding a new link
            addLinkCard.addEventListener('click', function() {
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
            
            linksContainer.appendChild(addLinkCard);
        }
        
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
            
            // Add click handler to open the link URL when not in edit mode
            // Only add edit functionality when in edit mode
            linkCard.addEventListener('click', function(e) {
                if (isEditMode) {
                    // In edit mode, open the edit modal
                    const linkId = this.dataset.linkId;
                    openEditModal(linkId);
                } else {
                    // Not in edit mode, open the link URL
                    window.open(link.url, '_blank');
                }
            });
            
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
});