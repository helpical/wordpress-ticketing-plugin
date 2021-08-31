
<img src="https://github.com/helpical/wordpress-ticketing-plugin/blob/master/assets/images/helpical-wordpress.png" alt="Helpical Wordpress Plugin">

# Introduction
Helpical is a commercial SaaS/Self-Hosted helpdesk and ticketing system written by Parnia Software Solutions. 
You can use our provided source codes here in this repository to connect your Wordpress websites to Helpical Ticketing System v1.x

### Steps to use this plugin
1. Upload the plugin ZIP file on the server of your Wordpress website (wp-contents/plugins directory) or use the Wordpress plugin management tool.
2. Install the plugin through the Wordpress.
3. Generate a new API secret key in Helpical Ticketing System (General settings)
4. Define the appropriate white list IPs in general settings of Helpical Ticketing System (Optional)
5. Put the generated API secret key and the URL of your ticketing system in plugin management page.
6. Press save button and make sure everything is done correctly. You will receive a shortcode in case of success.

### Modify CSS styles and JavaScript codes
By default, you don't need to change anything in the codes. However, if you would like to do changes on CSS or JavaScript files, please do these steps:
1. Run `npm install` to install dependencies.
2. Compile the JS codes in `src/js` directory by running `gulp js` command.
3. Compile the CSS codes in `src/scss` by running `gulp scss` command.

### Helpical RESTful APIs documents
If you need to modify the behavior of this Wordpress plugin by your needs, we invite you to visit https://apidocs.helpical.com to get familiar with the structure of Helpical RESTful APIS.

### Support & bug reporting
If you encounter with any possible issue and need our support services, or if you find any bug in this plugin, please contact us via admin@helpical.com.
Your cooperation is much appreciated.

### Contributors  
This plugin is a project by Helpical development and technical team.
Thanks with all of its contibutors:

- <a href="https://github.com/mbsaberi">Mohammad Saberi</a> (Helpical RESTful APIs developer)
- <a href="https://github.com/mohamad-momeni">Mohammad Momeni</a> (Wordpress plugin back-end developer)
- <a href="https://github.com/AlirezaAriyanpour">Alireza Ariyanpoor</a> (Wordpress plugin front-end developer)
- Ali Sheikhzadeh (Wordpress pluging UI/UX designer)
