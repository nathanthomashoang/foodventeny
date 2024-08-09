(async () => {
    const applicationsData = await fetchApplicationsData();

    if (applicationsData) {
        generateApplicationListItems(
            applicationsData,
            "#application_list_container"
        );
    }
})();

// TODO: Ideally, this should be moved into some sort of service or module for reusability
async function fetchApplicationsData() {
    try {
        const response = await fetch(`/api/applications`);

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        return response.json();
    } catch (error) {
        console.error(error);
    }
}

// TODO: this should be moved into some sort of component service for reusability

// NOTE: This method will dynamically create the components based on number of applications we have
function generateApplicationListItems(data, containerId) {
    const listContainer = document.querySelector(containerId);

    data.forEach(data => {
        const { id, title, deadline_date, status, type } = data;

        const anchor = document.createElement('a');
        anchor.href = `/applications/${id}/review`;
        anchor.className = 'list-group-item list-group-item-action';

        const divFlex = document.createElement('div');
        divFlex.className = 'd-flex w-100 justify-content-between';

        const h5 = document.createElement('h5');
        h5.className = 'mb-1';
        h5.id = 'title';
        h5.textContent = title;

        const smallDate = document.createElement('small');
        smallDate.id = 'deadline_date';
        smallDate.innerHTML = `<strong>Deadline Date: </strong><span id="deadline_date">${deadline_date}</span>`;

        divFlex.appendChild(h5);
        divFlex.appendChild(smallDate);

        const pStatus = document.createElement('p');
        pStatus.className = 'mb-1';
        pStatus.innerHTML = `<strong>Application Status: </strong><span id="status">${status}</span>`;

        const smallType = document.createElement('small');
        smallType.innerHTML = `<strong>Type: </strong><span id="type">${type}</span>`;

        anchor.appendChild(divFlex);
        anchor.appendChild(pStatus);
        anchor.appendChild(smallType);

        listContainer.appendChild(anchor);
    });
}