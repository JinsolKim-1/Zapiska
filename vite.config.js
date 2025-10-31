import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/home.css','resources/css/post-verification.css',
                'resources/js/delete_temp.js', 'resources/css/welcmain.css','resources/js/welcmain.js','resources/css/comp_ver.css'
                 ,'resources/js/phone.js','resources/css/login.css', 'resources/js/superadmin.js','resources/css/superadmin.css'
                 ,'resources/js/superadmin_users.js','resources/css/sidebar.css','resources/css/super_users.css','resources/css/companies.css'
                 ,'resources/css/mainsidebar.css','resources/css/userdashboard.css','resources/js/userdashboard.js'
                ,'resources/css/departments.css','resources/css/assets.css','resources/css/requests.css','resources/css/receipts.css'
                ,'resources/css/users.css','resources/css/sectorUsers.css','resources/css/addUser.css','resources/css/invite.css'
                ,'resources/css/inventory.css','resources/css/manager-requests.css','resources/css/manager-assets.css','resources/css/employee-requests.css'
                ,'resources/js/orderForm.js'],
            refresh: true,
        }),
    ],
});
