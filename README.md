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
    {fws any}content for any request{fws}
  </pre>
- Test it in a browser and in the Moodle app.

# Filtering by User Agent #

You can also specify a regular expression to filter the requests by user agent. To do so, you need to add a ua="MY_REGEXP" attribute to the {fws} tag, where MY_REGEXP is the regular expression you want to check (without starting and ending slashes). Example:

<pre>
    {fws ws ua="MoodleMobile\s?$" }content only for the official Moodle app{fws}
    {fws any ua="iPhone|iPad|iPod"}content only for iOS devices{fws}
</pre>
