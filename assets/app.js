
// Helper to compute totals on order page
function toPayment(e){
  e.preventDefault();
  const bw = parseInt(document.getElementById('bw').value||0);
  const color = parseInt(document.getElementById('color').value||0);
  const confirmed = document.getElementById('confirmOrder').checked;
  if(!confirmed){
    alert('Please confirm that you want to order and will pay.');
    return false;
  }
  const total = bw*1 + color*3;
  // pass values via query string to payment.html
  const params = new URLSearchParams();
  params.set('o_name', document.getElementById('o_name').value);
  params.set('o_roll', document.getElementById('o_roll').value);
  params.set('bw', bw);
  params.set('color', color);
  params.set('total', total);
  window.location = 'payment.html?' + params.toString();
  return false;
}

function goNext(e){
  // upload.html: just continue to confirm with query params
  e.preventDefault();
  const fn = document.getElementById('fullname').value;
  const roll = document.getElementById('roll').value;
  if(!fn||!roll){ alert('Please enter name and roll/ID'); return false; }
  const params = new URLSearchParams();
  params.set('fullname', fn);
  params.set('roll', roll);
  window.location = 'confirm.html?' + params.toString();
  return false;
}

function goConfirm(e){
  e.preventDefault();
  const urlp = new URLSearchParams(window.location.search);
  // forward via query to confirm.html: leave to user to select final options
  const form = document.getElementById('payForm');
  // read method, attach to query
  const pay_method = document.getElementById('pay_method')?document.getElementById('pay_method').value:'upi';
  window.location = 'confirm.html?pay_method=' + encodeURIComponent(pay_method) + '&' + urlp.toString();
  return false;
}

// On payment page, set quick links and preview QR if uploaded
document.addEventListener('change', function(ev){
  if(ev.target && ev.target.id === 'qr'){
    const f = ev.target.files[0];
    if(f){
      const img = document.createElement('img');
      img.style.maxWidth='220px';
      img.style.display='block';
      img.style.marginTop='8px';
      img.src = URL.createObjectURL(f);
      const p = document.getElementById('qrPreview');
      p.innerHTML='';
      p.appendChild(img);
    }
  }
});

// When payment.html loads, fill data and create quick pay links
window.addEventListener('load', function(){
  const url = new URL(location.href);
  const total = url.searchParams.get('total') || '';
  if(total){
    const el = document.createElement('p');
    el.textContent = 'Order total: Rs. ' + total;
    document.querySelector('.container').insertBefore(el, document.querySelector('.container').children[3]);
  }
  // quick links are application-specific deep links. We'll provide UPI intent and generic fallback.
  const upi = 'upi://pay?pa=8972548589@ibl&pn=STUROX&cu=INR';
  document.getElementById('phonepeLink').href = upi;
  document.getElementById('paytmLink').href = upi;
  document.getElementById('gpayLink').href = upi;
});

// Final page validations before sending to server
function checkFinal(e){
  // ensure confirm checkbox
  const method = document.getElementById('final_pay_method').value;
  const checked = document.getElementById('final_confirm').checked;
  if(!checked){
    alert('Please tick the confirmation checkbox to proceed.');
    e.preventDefault(); return false;
  }
  if(method === 'upi'){
    const fs = document.getElementById('final_pay_screenshot').files;
    if(!fs || fs.length === 0){
      alert('You selected online payment. Please upload the payment screenshot before submitting.');
      e.preventDefault(); return false;
    }
  }
  // allow submit; the form posts to upload.php (server-side)
  return true;
}

// Pre-fill fields on confirm.html if query params exist
window.addEventListener('DOMContentLoaded', function(){
  const urlp = new URLSearchParams(window.location.search);
  const fn = urlp.get('fullname') || urlp.get('o_name') || '';
  const roll = urlp.get('roll') || urlp.get('o_roll') || '';
  const pay_method = urlp.get('pay_method') || '';
  if(document.getElementById('f_name')) document.getElementById('f_name').value = fn;
  if(document.getElementById('f_roll')) document.getElementById('f_roll').value = roll;
  if(pay_method && document.getElementById('final_pay_method')) document.getElementById('final_pay_method').value = pay_method;
  if(urlp.get('total') && document.getElementById('order_total')) document.getElementById('order_total').value = urlp.get('total');
});
