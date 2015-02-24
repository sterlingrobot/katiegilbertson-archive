**g2responsive Module Readme.md**
---
2014-02-27 Rev.4

---
Introduction
--
The goal of the g2responsive module is to alter the behavior of the Xataface g2 module to adapt to smaller screens for a more mobile friendly application. It uses twitter bootstrap to make the app responsive to smaller screens. It aims to automatically create a mobile friendly version of your app that behaves like g2 on larger/desktop screens and adjusts the screen contents and layout for smaller screens.

It isn't meant to be a complete solution. It is meant to make it easier to create, find, and edit records on a mobile device.

The module also comes with the option of adding a 'dashboard' page which serves as a main access page to get to every other table. The dashboard contains no table information by default, so you can also use it as a page to display anything that you feel may be helpful to the application.

Note that there is a sample app called address141 that can be used as an example of how to use the g2responsive module.

---

**Installation:**

---

1. Install the g2 module as it is a prerequisite for this module.

2. Copy the g2responsive directory into your application's modules directory.

2. Add the following to the *[_modules]* section of your *conf.ini* file:

```
  modules_g2responsive=modules/g2responsive/g2responsive.php
```

Optional (to add the dashboard):

1. **IF** you do not have an *ApplicationDelegate.php* one will be create for you. **IF** you have an *ApplicationDelegate.php*, add the following to the *beforeHandleRequest()* function of your *ApplicationDelegate.php* file:

```
    //action to display dashboard...
    $app = & Dataface_Application::getInstance();
    $query = & $app->getQuery();
    if ($query['-table'] == 'dashboard' and ($query['-action'] == 'browse'
        or $query['-action'] == 'list')) {
        $query['-action'] = 'dashboard_action';
    }
```

2. In the module root, open the file *settings.ini* and change the value of *dashboard* to 1.

3. When you connect to your app, it will generate the table needed for the dashboard.

4. After loading the app at least once add the following to the top of the *[_tables]* section of your *conf.ini* file:

```
    dashboard="Dashboard"
```

---

**Adding/Removing Dashboard after Installation:**

---

Adding:

1. In the module root, open the file *settings.ini*, change the value of *dashboard* to 1 and *installed* to 0.

2. Connect to your app, refresh the page twice (waiting for it to fully load each time.)

3. Follow the Optional instructions in the **Installation** section.
4. set *installed* to 1 when all is complete.

Removing:

1. In the module root, open the file *settings.ini*, change the value of *dashboard* to 0 and *installed* to 0.

2. Remove the code from the respective files from the **Installation** section.

3. Connect to your app, refresh the page twice (waiting for it to fully load each time.)

---

**Editing the dashboard**

---

If you're going to edit the dashboard, copy the *dashboard.html*, included in the templates folder of the module to yourapp/templates. It contains special styles in it which should not be overwritten. If you already have a *dashboard.html*, copy the styles from the module dashboard into your app dashboard.html.

---

**Creating a record name for Mobile tables**

---

By default, the module will display the first user-created column in mobile view. In order to show a meaningful amount of information about the record in this column, I recommend creating a title or record_name for the record using the following approach:

Navigate to your individual table folders (located in yourapp/tables). Add the following to *fields.ini* for the table in question:

```
[record_name]
order=-1999
visibility:list=visible
```

Below is an example of what to add to *your_table.php* (where your_table is the name of the table in your application):

```
function __sql__()
    {
        return "SELECT * , ( concat_ws('_', SUBSTRING(name, 1, 4), SUBSTRING(phone, 1, 11)))  as record_name   FROM `contacts`  order by created desc";
    }
```

" FROM 'contacts' " is telling it to load from the contacts table. "(name, 1, 4)" is telling it to take the first 4 characters of the 'name' field, starting at character 1.

For example, someone has the name *Joe Smith* and the entry here was (name, 3, 5), what would be displayed would be 'e Smi'

---

By: David Gleba and Oliver Clarke