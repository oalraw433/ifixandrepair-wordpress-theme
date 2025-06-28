<?php get_header(); ?>

<div class="container">
    <div class="card">
        <div class="logo">
            <h1>iFixandRepair</h1>
            <p>Professional Device Repair Services</p>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" id="progressBar"></div>
        </div>

        <!-- Initial Selection -->
        <div class="question active" id="question0">
            <h2>Welcome! How can we help you today?</h2>
            <div class="option-buttons">
                <div class="option-button" onclick="selectService('newRepair')">
                    🔧 Schedule New Repair
                </div>
                <div class="option-button" onclick="selectService('pickup')">
                    📱 Pick Up My Device
                </div>
            </div>
        </div>

        <!-- Pickup Flow -->
        <div class="question" id="pickupQuestion">
            <h2>📱 Device Ready for Pickup</h2>
            <div class="input-group">
                <label>Please enter your phone number:</label>
                <input type="tel" id="pickupPhone" placeholder="(555) 123-4567" required>
            </div>
            <button class="next-btn" onclick="handlePickup()">Continue</button>
        </div>

        <div class="question" id="pickupSuccess">
            <div class="success-message">
                <h2>🎉 Thank You!</h2>
                <p>Your device is ready for pickup. Please show this confirmation to our staff.</p>
            </div>
            <div class="qr-code">
                <img src="data:image/svg+xml,%3Csvg width='150' height='150' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='150' height='150' fill='%23f0f0f0'/%3E%3Ctext x='75' y='80' text-anchor='middle' font-size='12' fill='%23333'%3EQR Code%3C/text%3E%3C/svg%3E" alt="QR Code for Review">
                <p style="margin-top: 10px; color: #666; font-size: 14px;">
                    Scan to leave us a Google review and help other customers!
                </p>
            </div>
            <button class="next-btn" onclick="resetForm()">Start New Request</button>
        </div>

        <!-- New Repair Flow -->
        <!-- Question 1: Customer Information -->
        <div class="question" id="question1">
            <h2>👋 Let's get your contact information</h2>
            <div class="suggested-customers" id="customerSuggestions"></div>
            <div class="input-group">
                <label>Full Name *</label>
                <input type="text" id="customerName" placeholder="John Doe" required>
            </div>
            <div class="input-group">
                <label>Phone Number *</label>
                <input type="tel" id="customerPhone" placeholder="(555) 123-4567" required>
            </div>
            <div class="input-group">
                <label>Email Address *</label>
                <input type="email" id="customerEmail" placeholder="john@example.com" required>
            </div>
            <button class="next-btn" onclick="nextQuestion(2)">Continue</button>
        </div>

        <!-- Question 2: Device Brand -->
        <div class="question" id="question2">
            <h2>📱 What device brand are you bringing in?</h2>
            <div class="option-buttons">
                <div class="option-button" onclick="selectBrand('Apple')">
                    🍎 Apple
                </div>
                <div class="option-button" onclick="selectBrand('Samsung')">
                    📱 Samsung
                </div>
                <div class="option-button" onclick="selectBrand('Other')">
                    🔧 Other Brand
                </div>
            </div>
            <button class="back-btn" onclick="previousQuestion(1)">Back</button>
            <button class="next-btn" onclick="nextQuestion(3)" id="brandNext" style="display:none;">Continue</button>
        </div>

        <!-- Question 3: Device Model -->
        <div class="question" id="question3">
            <h2>🔍 What's your specific device model?</h2>
            <div class="input-group">
                <label>Device Model *</label>
                <input type="text" id="deviceModel" placeholder="e.g., iPhone 14 Pro, Galaxy S23, etc." required>
            </div>
            <button class="back-btn" onclick="previousQuestion(2)">Back</button>
            <button class="next-btn" onclick="nextQuestion(4)">Continue</button>
        </div>

        <!-- Question 4: Device Issue -->
        <div class="question" id="question4">
            <h2>🔧 What issue are you experiencing?</h2>
            <div class="option-buttons">
                <div class="option-button" onclick="selectIssue('Broken Screen')">
                    📱 Broken/Cracked Screen
                </div>
                <div class="option-button" onclick="selectIssue('Other')">
                    ⚙️ Other Issue
                </div>
            </div>
            <div class="input-group" id="otherIssueInput" style="display:none;">
                <label>Please describe the issue *</label>
                <textarea id="issueDescription" placeholder="Brief description of the problem..." rows="3"></textarea>
            </div>
            <button class="back-btn" onclick="previousQuestion(3)">Back</button>
            <button class="next-btn" onclick="nextQuestion(5)" id="issueNext" style="display:none;">Continue</button>
        </div>

        <!-- Question 5: Quoted Price -->
        <div class="question" id="question5">
            <h2>💰 What price did our technician quote you?</h2>
            <div class="input-group">
                <label>Repair Quote (USD) *</label>
                <input type="number" id="quotedPrice" class="price-input" placeholder="0.00" min="0" step="0.01" required>
            </div>
            <button class="back-btn" onclick="previousQuestion(4)">Back</button>
            <button class="next-btn" onclick="nextQuestion(6)">Continue</button>
        </div>

        <!-- Question 6: QR Code Payment -->
        <div class="question" id="question6">
            <h2>💳 Save on Credit Card Fees!</h2>
            <p style="margin-bottom: 20px; color: #666;">Scan our QR code, leave a Google review, and we'll waive all credit card processing fees!</p>
            <div class="qr-code">
                <img src="data:image/svg+xml,%3Csvg width='150' height='150' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='150' height='150' fill='%23f0f0f0'/%3E%3Ctext x='75' y='80' text-anchor='middle' font-size='12' fill='%23333'%3EQR Code%3C/text%3E%3C/svg%3E" alt="QR Code for Review">
            </div>
            <div class="option-buttons">
                <div class="option-button" onclick="selectQROption('yes')">
                    ✅ Yes, I'll scan & review
                </div>
                <div class="option-button" onclick="selectQROption('no')">
                    ❌ No thanks, I'll pay normally
                </div>
            </div>
            <button class="back-btn" onclick="previousQuestion(5)">Back</button>
            <button class="next-btn" onclick="nextQuestion(7)" id="qrNext" style="display:none;">Continue</button>
        </div>

        <!-- Question 7: Accessories -->
        <div class="question" id="question7">
            <h2>🛡️ Protect Your Investment - 50% OFF!</h2>
            <p style="margin-bottom: 20px; color: #666;">Special offer with every repair - premium accessories at half price!</p>
            
            <div class="accessory-option" onclick="selectAccessory('case')">
                <div>
                    <strong>📱 Premium Phone Case</strong>
                    <br><small>Military-grade protection</small>
                </div>
                <div class="accessory-price">$10 (50% OFF)</div>
            </div>
            
            <div class="accessory-option" onclick="selectAccessory('screen')">
                <div>
                    <strong>🛡️ Tempered Glass Screen Protector</strong>
                    <br><small>9H hardness protection</small>
                </div>
                <div class="accessory-price">$10 (50% OFF)</div>
            </div>
            
            <div class="accessory-option" onclick="selectAccessory('none')">
                <div>
                    <strong>❌ No accessories</strong>
                    <br><small>Just the repair, please</small>
                </div>
                <div class="accessory-price">$0</div>
            </div>

            <button class="back-btn" onclick="previousQuestion(6)">Back</button>
            <button class="next-btn" onclick="nextQuestion(8)" id="accessoryNext" style="display:none;">Continue</button>
        </div>

        <!-- Question 8: Terms and Conditions -->
        <div class="question" id="question8">
            <h2>📋 Terms & Conditions</h2>
            <div class="terms-text">
                Device repairs deal with a large extent of electronic equipment and components. Warranty does not cover Broken screens, lines on the screen or water damage. There will be no returns or refunds to any sold part unless defective, we will attempt to repair first before refund. If the part is broken then the warranty will be voided. Our staff is not equipped to fully determine causes of malfunctions on a device purely by the outside. A Tech cannot fully determine cause until inside the device, which could result in a matter that the device may need further repair or may not be repairable at all. If the phone is not repairable and the original part has to be returned to the device, it may not be in the same condition. There is a diagnostic fee for all phones even when a device cannot be repaired. Phones not picked within 60 days become property of iFixandRepair. If a repair will take longer than scheduled, a representative of the store will contact you as soon as notified to update you on the status of your device. iFixandRepair is not liable for damages incurred prior to the device being left in our possession. Logic boards and Mother boards are not repairable and/or replaceable. WE ARE NOT RESPONSIBLE FOR accessories PLEASE TAKE ALL accessories.
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="agreeTerms" required>
                <label for="agreeTerms">I agree to the terms and conditions *</label>
            </div>
            <button class="back-btn" onclick="previousQuestion(7)">Back</button>
            <button class="submit-btn" onclick="submitForm()">Submit Repair Request</button>
        </div>

        <!-- Success Message -->
        <div class="question" id="successMessage">
            <div class="success-message">
                <h2>🎉 Repair Request Submitted!</h2>
                <p>Thank you for choosing iFixandRepair. We'll contact you shortly with updates on your device repair.</p>
            </div>
            <button class="next-btn" onclick="downloadInvoice()">📄 Download Invoice</button>
            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button class="next-btn" onclick="resetForm()" style="background: #28a745; flex: 1;">
                    👥 New Customer Form
                </button>
                <button class="next-btn" onclick="resetForm()" style="background: #6c757d; flex: 1;">
                    🔄 Another Request
                </button>
            </div>
        </div>
    </div>

    <!-- Admin Login -->
    <div class="admin-login">
        <input type="password" id="adminPassword" placeholder="Admin Password">
        <button onclick="loginAdmin()">Admin Panel</button>
    </div>

    <!-- Admin Panel -->
    <div class="admin-panel" id="adminPanel">
        <div class="card">
            <h2>Admin Panel - Customer Data</h2>
            <button onclick="exportToExcel()" style="margin-bottom: 20px; padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer;">Export to Excel</button>
            <table class="admin-table" id="adminTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Device</th>
                        <th>Issue</th>
                        <th>Price</th>
                        <th>QR Review</th>
                        <th>Accessories</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php get_footer(); ?>