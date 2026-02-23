# Display Customer Group Name

**Module Name:** swtdisplaycustomergroupname  
**Author:** Kaleem Ullah | SolverWebTech | Freelance  
**Version:** 1.0.0  
**Category:** Front Office Features  
**PrestaShop Compatibility:** 1.7.x – 9.x  

---

## 📌 Overview

Display Customer Group Name is a lightweight PrestaShop module that shows the logged-in customer's group name along with a profile icon.

It can be displayed in the header, customer account area, or anywhere using a custom hook.

This is especially useful for:

- B2B stores
- Wholesale platforms
- Multi-group pricing environments
- Membership-based stores

---

## 🚀 Features

- Displays logged-in customer's group name
- Optional profile icon support
- Custom hook support for flexible placement
- Bootstrap compatible
- Lightweight and performance-friendly
- PrestaShop 1.7 to 9 compatible

---

## 🛠 Installation

The module can be easily installed directly from the PrestaShop Admin Module Manager.

### Method 1 — Upload ZIP (Recommended)

1. Go to your **PrestaShop Back Office**
2. Navigate to: Modules → Module Manager
3. Click the **Upload a module** button (top right)
4. Upload the module ZIP file
5. After upload, click **Install**

---

### Method 2 — Manual Installation (FTP)

1. Extract the module folder: swtdisplaycustomergroupname
2. Upload it to: /modules/ directory of your PrestaShop installation
3. Go to: Back Office → Modules → Module Manager
4. Search for: Display Customer Group Name
5. Click **Install**

---

## 🎯 Custom Hooks

This module provides two custom hooks for flexible front-office integration.

These hooks allow you to display the customer profile icon and customer group name anywhere in your theme without modifying core files.

---

### 🔹 1. displaySwtCustomerProfileIcon

This hook renders only the **customer group profile icon**.

#### Usage in Smarty template:

```smarty
{hook h='displaySwtCustomerProfileIcon'}
```

#### Example:

```smarty
<span class="customer-group-icon">
    {hook h='displaySwtCustomerProfileIcon'}
</span>
```

### 🔹 2. displaySwtCustomerGroupName

This hook renders the **customer group name** of the logged-in customer.

#### Usage in Smarty template:

```smarty
{hook h='displaySwtCustomerGroupName'}
```

#### Example:

```smarty
<span class="customer-group-name">
    {hook h='displaySwtCustomerGroupName'}
</span>
```

---

## ⚙️ Important Behavior

If the module configuration enables JavaScript display mode:
Both hooks will return empty output to prevent duplicate rendering.

---

## 📍 Recommended Placement

You may include these hooks in:

- `header.tpl`
- `nav.tpl`
- `customer account templates`
- Custom theme templates

---

These custom hooks provide full flexibility for theme developers and store owners.

---

Developed and maintained by **Kaleem Ullah (SolverWebTech)** – Freelance PrestaShop Module Developer.
