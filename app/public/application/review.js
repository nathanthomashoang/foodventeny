(async () => {
    // NOTE/TODO: Parsing the path could be done more elegantly
    const applicationId = window.location.pathname.split('/')[2];
    const applicationData = await fetchApplicationDataById(applicationId);

    const {
        deadline_date,
        description,
        email,
        image,
        status,
        title,
        type
    } = applicationData;

    // setting status value for form value
    const statusFormEl = document.getElementById('status');
    statusFormEl.value = status;

    // TODO: Move this callback function for the listener into its own function
    document.getElementById('application_status_form').addEventListener('submit', async function (e) {
        // Ideally, we display some sort of loading indicator upon submitting form
        e.preventDefault();

        const formData = {
            ...applicationData,
            ['status']: statusFormEl.value,
        };

        $isUpdated = await updateApplicationData(formData);
        window.location.href = `/applications`;
    });

    const titleEl = document.getElementById('title');
    const deadlineDateEl = document.getElementById('deadline_date');
    const descriptionEl = document.getElementById('description');
    const emailEl = document.getElementById('email');
    const imageEl = document.getElementById('image');
    const typeEl = document.getElementById('type');

    titleEl.textContent = title;
    deadlineDateEl.textContent = deadline_date;
    descriptionEl.textContent = description;
    emailEl.textContent = email;
    imageEl.src = image;
    typeEl.textContent = type;

})();

// TODO: Ideally, all of the below should be moved into some sort of service or module for reusability and cleanliness
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

async function updateApplicationData(formData) {
    try {
        const response = await fetch(`/api/applications/${formData.id}`,
            {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            }
        );

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        return true;
    } catch (error) {
        console.error(error);
    }
}