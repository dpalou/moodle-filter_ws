WS filter plugin
====================================

With this plugin you can choose if some content should be visible only in web (browser) or only in a WebService client like the Moodle apps. The main purpose of this plugin is to be able to display/hide some content only for the Moodle app.

# To Install it manually #

- Unzip the plugin in the moodle .../filter/ directory.
- Enable it from "Site Administration >> Plugins >> Filters >> Manage filters".

# To Use it #
- Enclose the content you want to filter between {fws} tags:
  <pre>
    {fws web}content only for web{fws}
    {fws ws}content only for ws{fws}
  </pre>
- Test it in a browser and in the Moodle app.
