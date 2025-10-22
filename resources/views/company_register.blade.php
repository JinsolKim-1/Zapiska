<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Company - Verification Request</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  @vite(['resources/css/comp_ver.css','resources/js/phone.js'])
</head>
<body>

  <div class="company-form-container">
    <div class="form-header">
      <h1><i class='bx bxs-buildings'></i> Company Registration</h1>
      <p>Fill out the company details below. Once submitted, await for approval</p>
    </div>

      @if(session('success'))
        <div class="success-message">
            <i class='bx bx-check'></i> {{ session('success') }}
        </div>
      @endif

    <form class="company-form" method="POST" action="{{ route('company.store') }}">
      @csrf

      <div class="form-row">
        <div class="form-group">
          <label for="company_name">Company Name</label>
          <input type="text" id="company_name" name="company_name" placeholder="Enter your company name" required>
        </div>

        <div class="form-group">
          <label for="company_email">Company Email</label>
          <input type="email" id="company_email" name="company_email" placeholder="example@company.com" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="company_number">Contact Number</label>
          <input type="tel" id="company_number" name="company_number" placeholder="+1 123 456 7890"
           pattern="^\+?[0-9\s\-().]{7,20}$"
           title="Enter a valid international phone number" required>
        </div>

        <div class="form-group">
          <label for="company_website">Website</label>
          <input type="url" id="company_website" name="company_website" placeholder="https://yourcompany.com">
        </div>
      </div>

      <div class="form-group full-width">
        <label for="company_address">Address</label>
        <input type="text" id="company_address" name="company_address" placeholder="Enter full company address">
      </div>

      <div class="form-group full-width">
        <label for="company_desc">Company Description</label>
        <textarea id="company_desc" name="company_desc" placeholder="Briefly describe your company..." required></textarea>
      </div>

      <div class="submit-row">
        <button type="button" onclick="history.back()" class="back-btn">
          <i class='bx bx-arrow-back'></i> Back
        </button>
        <button type="submit" class="submit-btn">Submit for Verification</button>
      </div>
    </form>
  </div>

</body>
</html>
