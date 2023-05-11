const modalButton = document.querySelector('.child-export-button');
if (!modalButton) throw new Error('No Modal opener button found');

const modalId = modalButton.dataset.bsTarget;
if (!modalId) throw new Error('Broken Bootstrap modal');

const modalElement = document.querySelector(modalId);
if (!modalElement) throw new Error('Broken Bootstrap modal');

modalElement.addEventListener('show.bs.modal', createModalContent);
const body = modalElement.querySelector('.modal-body');

function createLiElements(data) {
  const ulElement = document.createElement('ul');
  ulElement.classList.add('row');

  data.forEach(element => {
    const li = document.createElement('li');
    li.classList.add('row', 'align-items-start');
    li.innerHTML = Joomla.sanitizeHtml(`
<h3 class="col mt-2">${element.template} [${element.client_id === 0 ? 'Site' : 'Administrator'}]</h3>
<button type="button" class="col btn btn-success" data-template="${element.template}" data-client-id="${element.client_id}">${Joomla.Text._('MOD_CHILDEXPORT_BUTTON_EXPORT')}</button>
<hr class="mt-1 mb-1">`);
    ulElement.appendChild(li);
  });

  ulElement.querySelectorAll('button').forEach(button => button.addEventListener('click', onExport));

  return ulElement;
}

 async function base64ToBlob(encoded) {
  let url = `data:application/zip;base64,${encoded}`;
  let res = await fetch(url);
  let blob = await res?.blob();
  return blob;
}

async function onExport(event) {
  const template = event.target.dataset.template;
  const client = event.target.dataset.clientId;
  const url = new URL(`${Joomla.getOptions('system.paths').baseFull}index.php?option=com_ajax&format=json&module=childexport&method=getZip&templateName=${template}&templateClient=${client}`);

  console.log({template, client});
  const response = await fetch(url, { headers: { 'X-CSRF-Token': Joomla.getOptions('csrf.token') || '' } });
  if (!response.ok) {
    // throw
  }

  const responseData = await response.json();
  console.log(responseData);
  if (responseData.data.blob) {
    const blob = await base64ToBlob(responseData.data.blob)
    // new Blob(atob(responseData.data.blob), {'type' : 'application/zip'});
    const urlBlob = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = urlBlob;
    link.innerText = urlBlob;
    link.download = `${template}_v${responseData.data.version}.zip`;
    event.target.parentNode.appendChild(link);
    link.click();
  }

  // Close the modal
  const mod = bootstrap.Modal.getInstance(modalElement)
  mod.hide();
}

async function createModalContent(event) {
  const url = new URL(`${Joomla.getOptions('system.paths').baseFull}index.php?option=com_ajax&format=json&module=childexport&method=getChilds`);
  body.innerHTML = '';
  const response = await fetch(url, { headers: { 'X-CSRF-Token': Joomla.getOptions('csrf.token') || '' } });
  console.log(response);

  if (!response.ok) {
    body.innerHTML = '<h3 class="ms-2">Oops, something went wrong...</h3>';
  }

  const responseData = await response.json();

  if (!responseData.data.length) {
    body.innerHTML = '<h3 class="ms-2">No child templates found</h3>';
    return;
  }

  body.appendChild(createLiElements(responseData.data));
}
