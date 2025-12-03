<script>
    document.addEventListener('DOMContentLoaded', function () {

        const currentPath = window.location.pathname;
        // const navbar =document.getElementById('navbar');

        const INACTIVE_CLASSES = ['hover:bg-gray-100', 'dark:hover:bg-gray-700'];
        const ACTIVE_CLASSES = ['bg-primary/10', 'text-primary', 'dark:bg-primary/20'];

        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            if (currentPath.endsWith(link.getAttribute('href'))) {
                link.classList.remove(...INACTIVE_CLASSES);
                link.classList.add(...ACTIVE_CLASSES);
            } else {
                link.classList.remove(...ACTIVE_CLASSES);
                link.classList.add(...INACTIVE_CLASSES);
            }
        });

    });

    // Get modal element
    const modal = document.querySelector('.fixed.inset-0');

    // Get all delete buttons
    const deleteButtons = document.querySelectorAll('button.nav-link');

    // Get cancel button inside the modal
    const cancelButton = modal.querySelector('button:nth-child(2)');

    // Show modal on delete button click
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    // Hide modal on cancel button click
    cancelButton.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
</script>