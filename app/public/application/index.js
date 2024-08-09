(async () => {
    const applicationId = window.location.pathname.split('/').pop();
    const applicationData = await fetchApplicationDataById(applicationId);

    // TODO: Properly handle and redirect to 404 page for resources that aren't available

    if (applicationData) {
        const {
            deadline_date,
            description,
            email,
            image,
            status,
            title,
            type
        } = applicationData;

        const titleEl = document.getElementById('title');
        const deadlineDateEl = document.getElementById('deadline_date');
        const descriptionEl = document.getElementById('description');
        const emailEl = document.getElementById('email');
        const imageEl = document.getElementById('image');
        const statusEl = document.getElementById('status');
        const typeEl = document.getElementById('type');


        titleEl.textContent = title;
        deadlineDateEl.textContent = deadline_date;
        descriptionEl.textContent = description;
        emailEl.textContent = email;
        imageEl.src = image;
        statusEl.textContent = status;
        typeEl.textContent = type;
    }
})();

// TODO: Ideally, this should be moved into some sort of service or module for reusability
async function fetchApplicationDataById(id) {
    try {
        const response = await fetch(`/api/applications/${id}`);

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        return response.json();
    } catch (error) {
        console.error(error);
    }
}