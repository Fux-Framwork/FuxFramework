---
sidebar_position: 2
---

# Configuration

Since FuxFramework CLI is under development, there is no automatic configuration process, this mean that you have to
manually edit some files in order to get the project working. Don't worry it's not hard to do and it doesn't takes more
than 2 minutes, **really**.

The minimum framework configuration require you to know just 1 simple thing: where your project folder is placed
relative to the domain root (or your localhost in case you are on your local web server). For example if your project
folder is named `MyWebApp` and you placed it directly under the `htdocs` folder your project (relative) directory
is `/MyWebApp`. If you are trying to deploy your web application on an hosting solution, directly under a specific
domain name. Your project (relative) directory is an empty string (meaning that the whole project is placed in the root
of the web server).

## The environment.php file

In your project `config` folder you should have a file called `environment.example.php`, just make a copy of it and
rename it to `environment.php`. After that just open it and edit it as explained in the following instructions:

1) Edit `SERVER_PORT` constant if your web server is listening to a specific port
2) Change the `PROJECT_DIR` constant with your project directory realtive to the web server root (with web server root
   we mean the `htdocs` folder or your web hosting root)
3) Change the `PROJECT_HTTP_SCHEMA` in order to match your preferences and web server configuration. Usually this
   constant can be untouched when developing in local web server, and changed to `https` when deploying your project
   online

## The .htaccess file
In your project root folder you should have a file called `example.htaccess`, just make a copy of it and
rename it to `.htaccess`. After that just open it and edit the `RewriteBase /fuxframework` line as following: 

- If your `PROJECT_DIR` constant (in environment.php file) is an empty string you can rewrite the line as `RewriteBase /`
- Else you have to change the `/fuxframework` path in the .htaccess in order to match with your `PROJECT_DIR`

## Project (relative) directory examples

The following examples better explain how to know your project relative directory in order to proceed with the
configuration process. Assuming that your named your project folder as `MyWebApp`, let's see some cases:

### Local web server (MAMP, XAMPP, etc...)

- Project is placed directly under `htdocs` folder, so you have somthing like `<...>/MAMP/htdocs/MyWebApp`. In this case
  your configuration is: `const PROJECT_DIR = "/MyWebApp"` inside the environment.php file and `RewriteBase /MyWebApp`
  inside .htaccess file
- Project is placed directly under `htdocs/SideProjects` folder, so you have somthing
  like `<...>/MAMP/htdocs/SideProjects/MyWebApp`. In this case your configuration
  is: `const PROJECT_DIR = "/SideProjects/MyWebApp"` inside the environment.php file
  and `RewriteBase /SideProjects/MyWebApp`
  inside .htaccess file
- If your `htdocs` folder directly contain all project files (this mean that in your htdocs folders there are no other
  files than skeleton files), your configuration is: `const PROJECT_DIR = ""` inside the environment.php file
  and `RewriteBase /`
  inside .htaccess file

### Production / online hosting

- If want to publish your project directly under your domain `mywebsite.com` (this mean that in your web hosting root
  folder there are no other files than your project files), your configuration is: `const PROJECT_DIR = ""` inside the
  environment.php file and `RewriteBase /` inside .htaccess file
- Project is placed inside a sub fodler in your web hosting root, so you have somthing like `mywebsite.com/MyWebApp`. In
  this case your configuration is: `const PROJECT_DIR = "/MyWebApp"` inside the environment.php file
  and `RewriteBase /MyWebApp` inside .htaccess file
- Project is placed in a deeper folder structure, for example you have `mywebsite.com/applications/MyWebapp`. In this
  case your configuration is: `const PROJECT_DIR = "/applications/MyWebapp"` inside the environment.php file
  and `RewriteBase /applications/MyWebapp`
  inside .htaccess file
