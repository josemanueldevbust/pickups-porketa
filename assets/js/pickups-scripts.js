   var currentStep = 0;
var locationSet = false;

var productItems = {};
var customerInfo = {};
var paymentInfo = {};
var orderSummary = {};
var state = {}
function setValue(name, value){
    state = {
        ...state, 
        [name]: value
    }
}
function getValue(name){
    return state[name]
}

function addToOrder(event, productId){
    event.preventDefault();
    event.stopPropagation();
    let current = productItems[productId] ?? 0
    productItems[productId] = current + 1;
    setValue('products', productItems)
    const product = products.find(p=>p.productId == productId)
    notify('Se agregó 1 ' + product.name, { type: 'success'})
    loadCartDetails()

}
function removeFromOrder(event, productId){
    event.preventDefault();
    event.stopPropagation();
    let current = productItems[productId] ?? 0
    productItems[productId] = current - 1;
    setValue('products', productItems)
    const product = products.find(p=>p.productId == productId)
    notify('Se quitó 1 ' + product.name)
    loadCartDetails()

    if(!stepValid(1)){
        notify('Agregue al menos un producto al carrito para continuar.', { type: 'info'})
        currentStep = 1;
        showStep();
    }
}
function loadOrderSummary(){
    let orderContainer = document.querySelector('.order-summary-list')
    const productCounts = getValue('products') ?? {}
    const totalAmount = Object.keys(productCounts).map(productId => {
        const product = products.find(p=>p.productId == productId)
        const count = productCounts[productId]
        return count * product.price
    })

    let locs = locations ?? []

    let location = locs.find(l=>l.id == getValue('location'))?.city ?? ''
    const totalvalue = Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(totalAmount.reduce((a, b) => a + b, 0))
    if(orderContainer){
        
        orderContainer.innerHTML = /*html*/`
            <li>Despacho desde:  ${location}</li>
            <li class="address" >
                <ul>
                    <li><div class="loc-img">
            <svg fill="#000000" width="800px" height="800px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.114-0.011c-6.559 0-12.114 5.587-12.114 12.204 0 6.93 6.439 14.017 10.77 18.998 0.017 0.020 0.717 0.797 1.579 0.797h0.076c0.863 0 1.558-0.777 1.575-0.797 4.064-4.672 10-12.377 10-18.998 0-6.618-4.333-12.204-11.886-12.204zM16.515 29.849c-0.035 0.035-0.086 0.074-0.131 0.107-0.046-0.032-0.096-0.072-0.133-0.107l-0.523-0.602c-4.106-4.71-9.729-11.161-9.729-17.055 0-5.532 4.632-10.205 10.114-10.205 6.829 0 9.886 5.125 9.886 10.205 0 4.474-3.192 10.416-9.485 17.657zM16.035 6.044c-3.313 0-6 2.686-6 6s2.687 6 6 6 6-2.687 6-6-2.686-6-6-6zM16.035 16.044c-2.206 0-4.046-1.838-4.046-4.044s1.794-4 4-4c2.207 0 4 1.794 4 4 0.001 2.206-1.747 4.044-3.954 4.044z"></path>
            </svg>
            </div><span> ${getValue('address')}</li>
            <li>Nombre del cliente: ${getValue('first_name')} ${getValue('last_name')}</li>
            <li>Email del cliente: ${getValue('email')}</li>
           
           
                </ul>
            </li>  
             <li class="prods">
                <h4>Pedido</h4>
                <ul class="product-list">
                    ${Object.keys(getValue('products') ?? {}).filter(productId => getValue('products')[productId] > 0).map(productId => /*html*/`
                        <li>
                            <div class="product-item">
                               <span>${products.find(p=>p.productId == productId)?.name}</span> x <span>${getValue('products')[productId]}</span>
                            </div>
                        </li>
                    `).join('')}
                </ul>
            </li> 
             <li class="billing">
                <ul>
                <li><h4>Detalles de pago</h4></li>
                <li><span>Total</span><span>${totalvalue}</span></li>
                <li class="separator"></li>

                    
                <li>Metodo de pago: ${getValue('payment_method')}</li>
                </ul>
             </li>
        `

    }

}

function loadCartDetails(){
    let cartContainer = document.querySelector('#cart-container')
    if(cartContainer){
        cartContainer.innerHTML = /*html*/`
    
        <h3>Pedido</h3>
        <ul class="product-list">
            ${Object.keys(getValue('products') ?? {}).filter(productId => getValue('products')[productId] > 0).map(productId => /*html*/`
                <li>
                    <div class="product-item">
                        <span>${products.find(p=>p.productId == productId)?.name}</span> x <span>${getValue('products')[productId]}</span> <span><button onclick="addToOrder(event, '${productId}')">+ Add</button></span><span><button ${productItems[productId]?'':"disabled"}onclick="removeFromOrder(event, '${productId}')"> - Remove</button></span>
                    </div>
                </li>
            `).join('')}
        </ul>
        `

    }
}

function toggleCart(){
    document.querySelector('#cart-container').classList.toggle('show');
    loadCartDetails();
}

function _ensurePickupsToaster(){
    if(window._pickupsToasterInitialized) return;
    window._pickupsToasterInitialized = true;

    const style = document.createElement('style');
    style.id = 'pickups-toaster-styles';
    style.textContent = `
    #pickups-toaster { position: fixed; right: 20px; bottom: 20px; z-index: 99999; display:flex; flex-direction:column-reverse; gap:12px; pointer-events:none; }
    .pickups-toast { pointer-events:auto; min-width:260px; max-width:380px; background:linear-gradient(135deg,#ffffff,#f8f9fb); color:#0f1720; border-radius:12px; box-shadow:0 8px 24px rgba(15,23,32,0.18); overflow:hidden; display:flex; gap:12px; align-items:flex-start; padding:12px; transform:translateY(12px) scale(.98); opacity:0; transition:transform .28s cubic-bezier(.2,.9,.2,1), opacity .28s ease; font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial; }
    .pickups-toast.show{ transform:translateY(0) scale(1); opacity:1; }
    .pickups-toast .pickups-icon{ width:40px; height:40px; flex:0 0 40px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:linear-gradient(180deg,#0ea5a4,#0b948b); color:white; }
    .pickups-toast .pickups-content{ flex:1 1 auto; display:flex; flex-direction:column; gap:4px; }
    .pickups-toast .pickups-title{ font-weight:600; font-size:14px; }
    .pickups-toast .pickups-message{ font-size:13px; color:#334155; }
    .pickups-toast .pickups-close{ margin-left:8px; background:transparent; border:0; color:#94a3b8; cursor:pointer; font-size:20px; line-height:1; padding:4px; }
    .pickups-toast.pickups-error .pickups-icon{ background:linear-gradient(180deg,#ef4444,#dc2626); }
    .pickups-toast.pickups-success .pickups-icon{ background:linear-gradient(180deg,#10b981,#059669); }
    .pickups-toast.pickups-info .pickups-icon{ background:linear-gradient(180deg,#3b82f6,#2563eb); }
    `;
    document.head.appendChild(style);

    const container = document.createElement('div');
    container.id = 'pickups-toaster';
    container.setAttribute('aria-live','polite');
    container.setAttribute('aria-atomic','false');
    document.body.appendChild(container);
}

function notify(message, options = {}){
    _ensurePickupsToaster();
    const container = document.getElementById('pickups-toaster');
    if(!container) return alert(message);

    const type = options.type || 'info'; // 'info' | 'success' | 'error'
    const duration = typeof options.duration === 'number' ? options.duration : 4200;

    const toast = document.createElement('div');
    toast.className = 'pickups-toast pickups-' + type;
    toast.setAttribute('role','status');

    const icon = document.createElement('div');
    icon.className = 'pickups-icon';
    // simple icons (SVG)
    const svgMap = {
        info: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" fill="rgba(255,255,255,0.12)"/><path d="M11 10h2v6h-2zm0-4h2v2h-2z" fill="currentColor"/></svg>',
        success: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" fill="currentColor"/></svg>',
        error: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM13 16h-2v-2h2v2zm0-4h-2V7h2v5z" fill="currentColor"/></svg>'
    };
    icon.innerHTML = svgMap[type] || svgMap.info;

    const content = document.createElement('div');
    content.className = 'pickups-content';
    const title = document.createElement('div');
    title.className = 'pickups-title';
    title.textContent = options.title || (type === 'success' ? 'Exitoso' : type === 'error' ? 'Error' : 'Mensaje');
    const msg = document.createElement('div');
    msg.className = 'pickups-message';
    msg.textContent = message;

    const closeBtn = document.createElement('button');
    closeBtn.className = 'pickups-close';
    closeBtn.setAttribute('aria-label','Cerrar');
    closeBtn.innerHTML = '&times;';

    content.appendChild(title);
    content.appendChild(msg);

    toast.appendChild(icon);
    toast.appendChild(content);
    toast.appendChild(closeBtn);

    container.appendChild(toast);

    // force a reflow to enable transition
    requestAnimationFrame(() => toast.classList.add('show'));

    let removed = false;
    function removeToast(){
        if(removed) return;
        removed = true;
        toast.classList.remove('show');
        toast.addEventListener('transitionend', () => {
            try { container.removeChild(toast); } catch(e){}
        }, { once: true });
    }

    closeBtn.addEventListener('click', (e) => { e.preventDefault(); removeToast(); });

    // auto remove
    const t = setTimeout(removeToast, duration);
    // clear timer if user closes early
    toast.addEventListener('remove', () => clearTimeout(t));

    // return an object to allow manual dismissal
    return {
        dismiss: removeToast,
        element: toast
    };
}
function openOrderNow(event){
    event.preventDefault();
    event.stopPropagation();
    console.log("Opening Order Now");
    currentStep = 0;
    locationSet = false;
    customerInfo = {};
    paymentInfo = {};
    productItems = {};
    
    state = {};
    orderSummary = {};
    document.querySelector('.order-now-container').classList.add('show');
    document.querySelector('#order-now-container').style.display = '';
    //showStep();
}

function openProductChooser(location){
    const content = window.locationTemplates[location]
    const ifr = document.querySelector('#order-iframe')
    ifr.contentDocument.write(content)
    ifr.style.setProperty('display','')

    document.querySelector('#order-now-container').style.display = 'none';

    setTimeout(()=>{
            ifr.contentWindow.loadProducts()
            ifr.contentWindow.ordersEndpoint = window.ordersEndpoint;
            ifr.contentWindow.ordersEndpointSimulate = window.ordersEndpointSimulate;
            ifr.contentWindow.ordersCaptureEndpoint = window.ordersCatureEndpoint;
            ifr.contentWindow.addEventListener('close-all', ()=>{
                ifr.contentDocument.write('')
                ifr.style.setProperty('display','none');
                document.querySelector('.order-now-container').classList.remove('show');
            })
    },1500)  
    
}
function setLocation(event, location){
    event.preventDefault();
    event.stopPropagation();
    console.log('Setting location to ' + location)
    //setValue('location', location);
    //locationSet = true;
    openProductChooser(location)
    
}
function stepValid(step){
    if(step === 0){
        return locationSet;
    }else if(step === 1){
        let products = getValue('products') ?? {};
        return Object.keys(products).filter(productId => products[productId] > 0).length > 0;
    }else if(step === 2){
        let userData = {
            first_name: getValue('first_name'),
            last_name: getValue('last_name'),
            email: getValue('email'),
            address: getValue('address'),
            phone: getValue('phone'),
        }
        for(let key in userData){
            if(!userData[key]){
                return false;
            }
        }
    }else if(step === 3){
        let payment = {
            payment_method: getValue('payment_method'),
        }
        for(let key in payment){
            if(!payment[key]){
                return false;
            }
        }
    }
    // Implement validation logic for each step
    return true; // Placeholder: always valid
}

function showStep(){
    const container = document.getElementById('order-now-container');
    container.querySelectorAll('.step').forEach(step => step.classList.remove('active-step'));
    const step = container.querySelector('[data-step="' + currentStep + '"  ]');
    if(!step) return;
    step.scrollIntoView({ behavior: 'smooth' });
    step.classList.add('active-step');
    loadOrderSummary()
}

function nextStep(){
    changeStep(1);
    
}

function getInvalidMessage(){
    switch(currentStep){
        case 1:
            return 'Por favor agregue al menos un producto al carrito para continuar.';
        case 2:
            return 'Por favor complete todos los campos para continuar.';
        case 3:
            return 'Por favor seleccione un metodo de pago para continuar.';
        default:
            return '';
    
            
    }
}
function changeStep(direction){
    if(direction === 1 && !stepValid(currentStep)){
        notify(getInvalidMessage());
        return;
    }
    currentStep += direction;
    showStep();
     const container = document.getElementById('order-now-container');
    const totalSteps = container.querySelectorAll('.step').length

    if(currentStep >= totalSteps){
        completeOrder();
    }
}
function closeOrderNow(event=null){
    if(event){

    event.preventDefault();
    event.stopPropagation();
    }
    currentStep = 0;
    document.querySelector('.order-now-container').classList.remove('show');
    console.log("Closing Order Now");
    
    currentStep = 0;
    locationSet = false;
    customerInfo = {};
    paymentInfo = {};
    productItems = {};
    document.querySelector('#cart-container').classList.remove('show')
    state = {};
    orderSummary = {};
    
}

function completeOrder(){
    if(!stepValid(currentStep)){
        notify("Please complete the current step before proceeding.");
        return;
    }
    // Collect all data and submit the order
    notify("Order completed successfully!");
    close()
}


function processOrder(event, url){
    event.preventDefault();
    event.stopPropagation();

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(state)
    }).then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Network response was not ok');
        }
    }).then(data => {
        notify(data['message'], { type: data['success'] ? 'success' : 'error' });
        if(data['success']){
            state.id = data['id'];
            localStorage.setItem('currentOrder', JSON.stringify(state))
        }
        closeOrderNow();
    }).catch(error => {
        notify(error.message,{ type: 'error'});
    });



}