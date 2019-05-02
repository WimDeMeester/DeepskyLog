# ToDos for DeepskyLog.laravel

## TESTS

## AUTHENTICATION

+ [ ] Do we need maileclipse?
+ [ ] Write user settings page and table for the DeepskyLog information
+ [ ] Write show observer
+ [ ] Write script to convert old observers table of DeepskyLog to laravel
+ [ ] Write script to convert old lenses table of DeepskyLog to laravel
+ [ ] Show one lens (show.blade.php)
  + [ ] Add link to the page of the observer
+ [ ] Clean up source code

## OBSERVATIONS

+ [ ] Lenses
  + [ ] Only show delete button if there are no observations
    + [ ] Also update the tests for lens
  + [ ] Recalculate number of observations for each lens of the observer whenever (needed for datatables?):
    + [ ] Add observation
    + [ ] Update observation
    + [ ] Delete observation 
+ [ ] Users
  + [ ] Only show delete button if there are no observations
  + [ ] Show number of observations, instruments and lists

## SEEDER