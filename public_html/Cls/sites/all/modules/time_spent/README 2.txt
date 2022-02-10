This module tracks, by Ajax, how long a registered user took to read a page and the time spent on the entire site.

You can configure which content type and role should be tracked and view the report, exhibiting the total hours (or minutes/seconds) a user spent reading a page. The interval between one Ajax call and other can be configurable trough UI also.

There isn't any module dependency.

USE CASE:
- You need to track and exhibit a statistic report with the amount of time a registered user took reading a node page and also the entire time he spent on your site.
- Module creates the path - admin/reports/time_spent/timespent-list-users which lists all the users along with the total time spent by them in tabular format.
- Path admin/reports/time_spent/timespent-list-users has autocomplete user search textfield which helps you in getting the complete report of that user.

BENCHMARK
A test server will have each ajax call varying about 110-130 milliseconds.

SPEEDING UP:
In Drupal 6, if you install High-performance JavaScript callback handler http://drupal.org/project/js, everything will be faster. Install it and clean cache. Time Spent is already pre configured to work with this module. With this integration, a test server will have each ajax call varying about 30-40 milliseconds.

LIMITATIONS:
- This module only tracks node pages and registered users. Taxonomy or views pages aren't tracked, but can be in later versions.
- If you need to track anonymous visits, consider using Google Analytics (GA can't track registered users by its privacy terms).
- If you have cache mode on, you need to use hook_boot instead. Change for time_spent_boot where it is time_spent_init.
- At this moment, people can fool the system if the user has Java Script knowledge and access directly the url time_spent/1 and hit refresh a couple of times. The system will think the user was on node/1 for a couple of minutes.

RECOMMENDATIONS:
Session Aggregator http://drupal.org/project/session_aggregator
Can track the total time a user has spent on the

Automated Logout http://drupal.org/project/autologout
Provides a site administrator the ability to log users out after a specified time of inactivity.

Session Limit http://drupal.org/project/session_limit
Allows administrators to limit the number of simultaneous sessions per user.

Obs.: These modules aren't required, but if you need to track the amount of time a user spent, probably you'll like them also.
