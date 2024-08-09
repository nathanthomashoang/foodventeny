(async () => {
    // TODO: Move this callback function for the listener into its own function

    const form = document.getElementById('new_application_form');

    form.addEventListener('submit', async function (e) {
        // NOTE: Ideally, we display some sort of loading indicator upon submitting form
        e.preventDefault();

        let formData = {};

        const formInputs = form.querySelectorAll('input, select, textarea');

        formInputs.forEach((input) => {
            const { name, value } = input;
            if (input.type !== "submit") {
                formData = {
                    ...formData,
                    [name]: value,
                }
            }
        });

        const submitBtn = document.getElementById('submit_btn');
        submitBtn.disabled = true;

        const application = await createNewApplication(formData);

        if (application) {
            window.location.href = `/applications/${application.id}`
        }
    });
})();

// TODO: Ideally, all of the below should be moved into some sort of service or module for reusability and cleanliness

async function createNewApplication(formData) {
    try {
        const response = await fetch('/api/applications/',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            }
        );

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        return response.json();
    } catch (error) {
        console.error(error);
    }
}