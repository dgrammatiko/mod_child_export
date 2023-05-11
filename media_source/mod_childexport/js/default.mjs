if (!Joomla || !Joomla.Text || !Joomla.getOptions || !Joomla.sanitizeHtml) {
  throw new Error('Joomla API is missing');
}
const modalButton = document.querySelector('.child-export-button');
if (!modalButton) throw new Error('No Modal opener button found');
const modalId = modalButton.dataset.bsTarget;
if (!modalId) throw new Error('Broken Bootstrap modal');
const modalElement = document.querySelector(modalId);
if (!modalElement) throw new Error('Broken Bootstrap modal');
const body = modalElement.querySelector('.modal-body');

async function onExport(event) {
  const url = new URL(`${Joomla.getOptions('system.paths').baseFull}index.php?option=com_ajax&format=json&module=childexport&method=getZip&templateName=${event.target.dataset.template}&templateClient=${event.target.dataset.clientId}`);
  const response = await fetch(url, { headers: { 'X-CSRF-Token': Joomla.getOptions('csrf.token') || '' } });
  if (!response.ok) {
    throw new Error('Not authorised?');
  }

  const responseData = await response.json();
  if (responseData.data.blob) {
    const blob = await base64ToBlob(responseData.data.blob);
    const urlBlob = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = urlBlob;
    link.innerText = urlBlob;
    link.download = `${event.target.dataset.template}_v${responseData.data.version}.zip`;
    link.click();
  }

  // Close the modal
  const mod = bootstrap.Modal.getInstance(modalElement);
  mod.hide();
}

function createLiElements(data) {
  const ulElement = document.createElement('ul');
  ulElement.classList.add('row');

  data.forEach((element) => ulElement.insertAdjacentHTML('afterbegin', Joomla.sanitizeHtml(`<li class="row align-items-start">
<h3 class="col mt-2">${element.template} [${element.client_id === 0 ? 'Site' : 'Administrator'}]</h3>
<button type="button" class="col btn btn-success" data-template="${element.template}" data-client-id="${element.client_id}">${Joomla.Text._('MOD_CHILDEXPORT_BUTTON_EXPORT')}</button>
<hr class="mt-1 mb-1"></li>`)));

  ulElement.querySelectorAll('button').forEach((button) => button.addEventListener('click', onExport));

  return ulElement;
}

async function base64ToBlob(encoded) {
  const url = `data:application/zip;base64,${encoded}`;
  const res = await fetch(url);
  const blob = await res?.blob();
  return blob;
}

async function createModalContent() {
  let responseData;
  let response;
  const url = new URL(`${Joomla.getOptions('system.paths').baseFull}index.php?option=com_ajax&format=json&module=childexport&method=getChilds`);
  body.innerHTML = '';
  try {
    response = await fetch(url, { headers: { 'X-CSRF-Token': Joomla.getOptions('csrf.token') || '' } });
  } catch (e) {
    body.innerHTML = '<h3 class="ms-2">Oops, something went wrong...</h3>';
    return;
  }

  if (!response || !response.ok) {
    body.innerHTML = '<h3 class="ms-2">Oops, something went wrong...</h3>';
    return;
  }
  try {
    responseData = await response.json();
  } catch (e) {
    body.innerHTML = '<h3 class="ms-2">No child templates found</h3>';
    return;
  }
  if (!responseData.data || !responseData.data.length) {
    body.innerHTML = '<h3 class="ms-2">No child templates found</h3>';
    return;
  }

  body.appendChild(createLiElements(responseData.data));
}

modalElement.addEventListener('show.bs.modal', createModalContent);
