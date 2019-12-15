after git checkout:

1) composer install
2) create .env file from .env.example
    the code uses mysql so you'll have to set up mysql access in .env
    default DB access is ascensor:ascensor:ascensor
3) php artisan key:generate
    just to make laravel happy
4) php artisan migrate
    creates tables in DB
5) php artisan serve
    launches dev server on localhost:8000
6) curl localhost:8000/api/wipe?cnt=4
    (or just open the url in browser without using curl)
    initializes a clean new run
7) curl localhost:8000/api/tick
    (or just open the url in browser without using curl)
    processes 1 minute (seeks forward if there is no calls during current minute)
8) repeat 6) until you get out of service error
    the ascensors stop acepting calls after last call scheme ends (20h in this case
    pending calls, if any, will still be served 
9) repeat from 6), try different count of ascensors

Notes:

1) there is no frontend, just an API with only 2 methods: wipe and tick
2) there's a default 60:1 rate limit on the API. Just wait a minute if you get an html error regarding this. Could be switched off. 
3) the responses do not include the travel done during current run. An easy solution would be to make a field in the Ascensor model, reset this field in all models at the beginning of tick command, and mark it in the fulfillCall() method. However a better solution would be not to store this value (redundant) but keep track of current movement separately, and return that from the command, Or even keep moves in DB as a log so the controller could get them back. Not dedicating time for this.
4) number of ascensors is totally dynamic. Indeed the current call scheme could be served by just 3 or maybe 2 ascensors
5) the system preserves calls until served so calls which cannot be served during current run are kept in DB and will be served in next round.
6) ascensor is not an english word. Should have used elevator and could be changed in ~15 minutes
7) there are no tests due to lack of time. All code apart from controllers could be unit tested within few hours. 

Possible improvements:
1) selection could prefer longest waiting call to be more fair
2) selection should use strategy patten and be replaceable (extract nearestTo() from AscensorManager and inject it into AscensorManager as a dependency), but again, matter of worktime
3) pending calls could be reused, eg. not create a new call with same from0-to1 parameters is there is already one same from0-to1 pending
4) call saving could be more dynamic, eg save only pending calls. Would save a lot of server work and could totally decouple state storing from logic. However current design is more robust, eg no created calls will be lost if there is an error while processing calls.
5) would be more readable return the lastMinute field in hour:minute format
6) $column field in Ascensor is redundant in current code, should be removed
7) models should be more organized in their folder. (but that's where laravel scaffolding creates it)
