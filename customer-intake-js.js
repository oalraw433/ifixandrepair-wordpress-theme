// Global variables
let currentQuestion = 0;
let totalQuestions = 8;
let formData = {};
let customerDatabase = [
    {name: "John Smith", phone: "(555) 123-4567", email: "john@email.com"},
    {name: "Sarah Johnson", phone: "(555) 987-6543", email: "sarah@email.com"},
    {name: "Mike Davis", phone: "(555) 456-7890", email: "mike@email.com"}
];
let submittedForms = [];

// Try to load from localStorage if available
try {
    submittedForms = JSON.parse(localStorage.getItem('repairForms') || '[]');
} catch (e) {
    submittedForms = [];
}

// Progress bar update
function updateProgressBar() {
    const progress = (currentQuestion / totalQuestions) * 100;
    const progressBar = document.getElementById('progressBar');
    if (progressBar) {
        progressBar.style.width = progress + '%';
    }
}

// Service selection
function selectService(service) {
    if (service === 'newRepair') {
        showQuestion(1);
        currentQuestion = 1;
        totalQuestions = 8;
    } else {
        document.getElementById('question0').classList.remove('active');
        document.getElementById('pickupQuestion').classList.add('active');
    }
    updateProgressBar();
}

// Show specific question
function showQuestion(questionNum) {
    document.querySelectorAll('.question').forEach(q => q.classList.remove('active'));
    const targetQuestion = document.getElementById('question' + questionNum) || document.getElementById(questionNum);
    if (targetQuestion) {
        targetQuestion.classList.add('active');
        if (typeof questionNum === 'number') {
            currentQuestion = questionNum;
        }
        updateProgressBar();
    }
}

// Navigation functions
function nextQuestion(questionNum) {
    if (validateCurrentQuestion()) {
        showQuestion(questionNum);
    }
}

function previousQuestion(questionNum) {
    showQuestion(questionNum);
}

// Customer search and suggestions
function searchCustomers() {
    const nameElement = document.getElementById('customerName');
    const phoneElement = document.getElementById('customerPhone');
    const emailElement = document.getElementById('customerEmail');
    
    if (!nameElement || !phoneElement || !emailElement) return;
    
    const name = nameElement.value.toLowerCase();
    const phone = phoneElement.value;
    const email = emailElement.value.toLowerCase();
    
    const suggestions = customerDatabase.filter(customer => 
        customer.name.toLowerCase().includes(name) ||
        customer.phone.includes(phone) ||
        customer.email.toLowerCase().includes(email)
    );
    
    displayCustomerSuggestions(suggestions);
}

function displayCustomerSuggestions(suggestions) {
    const container = document.getElementById('customerSuggestions');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (suggestions.length > 0) {
        suggestions.forEach(customer => {
            const div = document.createElement('div');
            div.className = 'customer-suggestion';
            div.innerHTML = `<strong>${customer.name}</strong><br>${customer.phone} - ${customer.email}`;
            div.onclick = () => fillCustomerData(customer);
            container.appendChild(div);
        });
    }
}

function fillCustomerData(customer) {
    const nameElement = document.getElementById('customerName');
    const phoneElement = document.getElementById('customerPhone');
    const emailElement = document.getElementById('customerEmail');
    const suggestionsElement = document.getElementById('customerSuggestions');
    
    if (nameElement) nameElement.value = customer.name;
    if (phoneElement) phoneElement.value = customer.phone;
    if (emailElement) emailElement.value = customer.email;
    if (suggestionsElement) suggestionsElement.innerHTML = '';
}

// Event listeners for customer search
document.addEventListener('DOMContentLoaded', function() {
    ['customerName', 'customerPhone', 'customerEmail'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', searchCustomers);
        }
    });
    updateProgressBar();
    updateAdminTable();
});

// Selection functions
function selectBrand(brand) {
    formData.brand = brand;
    document.querySelectorAll('#question2 .option-button').forEach(btn => btn.classList.remove('selected'));
    event.target.classList.add('selected');
    const nextBtn = document.getElementById('brandNext');
    if (nextBtn) nextBtn.style.display = 'block';
}

function selectIssue(issue) {
    formData.issue = issue;
    document.querySelectorAll('#question4 .option-button').forEach(btn => btn.classList.remove('selected'));
    event.target.classList.add('selected');
    
    const otherIssueInput = document.getElementById('otherIssueInput');
    const issueDescription = document.getElementById('issueDescription');
    const nextBtn = document.getElementById('issueNext');
    
    if (issue === 'Other') {
        if (otherIssueInput) otherIssueInput.style.display = 'block';
        if (issueDescription) issueDescription.required = true;
    } else {
        if (otherIssueInput) otherIssueInput.style.display = 'none';
        if (issueDescription) issueDescription.required = false;
    }
    if (nextBtn) nextBtn.style.display = 'block';
}

function selectQROption(option) {
    formData.qrReview = option;
    document.querySelectorAll('#question6 .option-button').forEach(btn => btn.classList.remove('selected'));
    event.target.classList.add('selected');
    const nextBtn = document.getElementById('qrNext');
    if (nextBtn) nextBtn.style.display = 'block';
}

function selectAccessory(accessory) {
    formData.accessory = accessory;
    document.querySelectorAll('.accessory-option').forEach(btn => btn.classList.remove('selected'));
    event.target.classList.add('selected');
    const nextBtn = document.getElementById('accessoryNext');
    if (nextBtn) nextBtn.style.display = 'block';
}

// Validation
function validateCurrentQuestion() {
    switch(currentQuestion) {
        case 1:
            const name = document.getElementById('customerName')?.value;
            const phone = document.getElementById('customerPhone')?.value;
            const email = document.getElementById('customerEmail')?.value;
            if (!name || !phone || !email) {
                alert('Please fill in all required fields.');
                return false;
            }
            formData.customerName = name;
            formData.customerPhone = phone;
            formData.customerEmail = email;
            return true;
        case 3:
            const model = document.getElementById('deviceModel')?.value;
            if (!model) {
                alert('Please enter your device model.');
                return false;
            }
            formData.deviceModel = model;
            return true;
        case 4:
            if (formData.issue === 'Other') {
                const description = document.getElementById('issueDescription')?.value;
                if (!description) {
                    alert('Please describe the issue.');
                    return false;
                }
                formData.issueDescription = description;
            }
            return true;
        case 5:
            const price = document.getElementById('quotedPrice')?.value;
            if (!price || price <= 0) {
                alert('Please enter a valid quoted price.');
                return false;
            }
            formData.quotedPrice = parseFloat(price);
            return true;
        case 8:
            const agreeTerms = document.getElementById('agreeTerms');
            if (!agreeTerms || !agreeTerms.checked) {
                alert('Please agree to the terms and conditions.');
                return false;
            }
            return true;
        default:
            return true;
    }
}

// Pickup handling
function handlePickup() {
    const phone = document.getElementById('pickupPhone')?.value;
    if (!phone) {
        alert('Please enter your phone number.');
        return;
    }
    const pickupQuestion = document.getElementById('pickupQuestion');
    const pickupSuccess = document.getElementById('pickupSuccess');
    if (pickupQuestion) pickupQuestion.classList.remove('active');
    if (pickupSuccess) pickupSuccess.classList.add('active');
}

// Form submission
function submitForm() {
    if (!validateCurrentQuestion()) return;
    
    const submission = {
        ...formData,
        submissionDate: new Date().toLocaleString(),
        id: Date.now()
    };
    
    submittedForms.push(submission);
    try {
        localStorage.setItem('repairForms', JSON.stringify(submittedForms));
    } catch (e) {
        console.log('LocalStorage not available, data stored in memory only');
    }
    
    // Submit to WordPress if available
    if (typeof repairFormAjax !== 'undefined' && typeof jQuery !== 'undefined') {
        jQuery.ajax({
            url: repairFormAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'submit_repair_form',
                customer_name: formData.customerName,
                customer_phone: formData.customerPhone,
                customer_email: formData.customerEmail,
                device_brand: formData.brand,
                device_model: formData.deviceModel,
                issue: formData.issue,
                issue_description: formData.issueDescription || '',
                quoted_price: formData.quotedPrice,
                qr_review: formData.qrReview,
                accessory: formData.accessory || 'none',
                nonce: repairFormAjax.nonce
            },
            success: function(response) {
                console.log('Form submitted to WordPress successfully');
            },
            error: function(error) {
                console.log('WordPress submission failed:', error);
            }
        });
    }
    
    document.querySelectorAll('.question').forEach(q => q.classList.remove('active'));
    const successMessage = document.getElementById('successMessage');
    if (successMessage) successMessage.classList.add('active');
    updateAdminTable();
}

// Invoice generation
function downloadInvoice() {
    const invoice = generateInvoiceHTML();
    const blob = new Blob([invoice], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `invoice_${formData.customerName.replace(/\s/g, '_')}_${Date.now()}.html`;
    a.click();
    URL.revokeObjectURL(url);
}

function generateInvoiceHTML() {
    let accessoryCost = 0;
    let accessoryItem = 'None';
    
    if (formData.accessory === 'case') {
        accessoryCost = 10;
        accessoryItem = 'Premium Phone Case';
    } else if (formData.accessory === 'screen') {
        accessoryCost = 10;
        accessoryItem = 'Tempered Glass Screen Protector';
    }
    
    const subtotal = formData.quotedPrice + accessoryCost;
    const creditCardFee = formData.qrReview === 'yes' ? 0 : subtotal * 0.03;
    const total = subtotal + creditCardFee;
    
    return `
<!DOCTYPE html>
<html>
<head>
    <title>iFixandRepair Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .line-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .total { font-weight: bold; font-size: 18px; border-top: 2px solid #333; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>iFixandRepair</h1>
        <p>Professional Device Repair Services</p>
        <p>Invoice #${Date.now()}</p>
        <p>Date: ${new Date().toLocaleDateString()}</p>
    </div>
    
    <div class="invoice-details">
        <h3>Customer Information</h3>
        <p><strong>Name:</strong> ${formData.customerName}</p>
        <p><strong>Phone:</strong> ${formData.customerPhone}</p>
        <p><strong>Email:</strong> ${formData.customerEmail}</p>
        <p><strong>Device:</strong> ${formData.brand} ${formData.deviceModel}</p>
        <p><strong>Issue:</strong> ${formData.issue}${formData.issueDescription ? ' - ' + formData.issueDescription : ''}</p>
    </div>
    
    <div class="line-item">
        <span>Device Repair</span>
        <span>$${formData.quotedPrice.toFixed(2)}</span>
    </div>
    
    <div class="line-item">
        <span>Accessory: ${accessoryItem}</span>
        <span>$${accessoryCost.toFixed(2)}</span>
    </div>
    
    <div class="line-item">
        <span>Credit Card Fee ${formData.qrReview === 'yes' ? '(Waived - Thank you for your review!)' : '(3%)'}</span>
        <span>$${creditCardFee.toFixed(2)}</span>
    </div>
    
    <div class="line-item total">
        <span>Total Amount</span>
        <span>$${total.toFixed(2)}</span>
    </div>
    
    <p style="margin-top: 30px; text-align: center; color: #666;">
        Thank you for choosing iFixandRepair!<br>
        We'll contact you with updates on your repair.
    </p>
</body>
</html>`;
}

// Reset form
function resetForm() {
    formData = {};
    currentQuestion = 0;
    totalQuestions = 8;
    
    // Reset all form inputs
    document.querySelectorAll('input, textarea, select').forEach(input => {
        if (input.type === 'checkbox') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
    
    // Reset all selections
    document.querySelectorAll('.option-button, .accessory-option').forEach(btn => {
        btn.classList.remove('selected');
    });
    
    // Hide conditional elements
    const otherIssueInput = document.getElementById('otherIssueInput');
    if (otherIssueInput) otherIssueInput.style.display = 'none';
    
    ['brandNext', 'issueNext', 'qrNext', 'accessoryNext'].forEach(id => {
        const element = document.getElementById(id);
        if (element) element.style.display = 'none';
    });
    
    // Show initial question
    document.querySelectorAll('.question').forEach(q => q.classList.remove('active'));
    const initialQuestion = document.getElementById('question0');
    if (initialQuestion) initialQuestion.classList.add('active');
    updateProgressBar();
}

// Admin functions
function loginAdmin() {
    const passwordElement = document.getElementById('adminPassword');
    if (!passwordElement) return;
    
    const password = passwordElement.value;
    if (password === 'Vded6273@') {
        const adminPanel = document.getElementById('adminPanel');
        if (adminPanel) adminPanel.style.display = 'block';
        passwordElement.value = '';
        updateAdminTable();
    } else {
        alert('Incorrect password');
    }
}

function updateAdminTable() {
    const tbody = document.getElementById('adminTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    submittedForms.forEach((form, index) => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${form.submissionDate}</td>
            <td>${form.customerName}</td>
            <td>${form.customerPhone}</td>
            <td>${form.customerEmail}</td>
            <td>${form.brand} ${form.deviceModel}</td>
            <td>${form.issue}${form.issueDescription ? ' - ' + form.issueDescription : ''}</td>
            <td>$${form.quotedPrice}</td>
            <td>${form.qrReview === 'yes' ? 'Yes' : 'No'}</td>
            <td>${form.accessory || 'None'}</td>
            <td><button class="download-btn" onclick="downloadFormInvoice(${index})">Download</button></td>
        `;
    });
}

function downloadFormInvoice(index) {
    const form = submittedForms[index];
    const tempFormData = formData;
    formData = form;
    downloadInvoice();
    formData = tempFormData;
}

function exportToExcel() {
    if (submittedForms.length === 0) {
        alert('No data to export');
        return;
    }
    
    let csvContent = 'Date,Name,Phone,Email,Device Brand,Device Model,Issue,Issue Description,Quoted Price,QR Review,Accessory\n';
    
    submittedForms.forEach(form => {
        csvContent += [
            form.submissionDate,
            form.customerName,
            form.customerPhone,
            form.customerEmail,
            form.brand,
            form.deviceModel,
            form.issue,
            form.issueDescription || '',
            form.quotedPrice,
            form.qrReview,
            form.accessory || 'None'
        ].map(field => `"${field}"`).join(',') + '\n';
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `repair_data_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    URL.revokeObjectURL(url);
}