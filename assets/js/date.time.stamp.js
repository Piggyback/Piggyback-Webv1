function initDatePrototype () {

    /*
     * this function takes String in
     *      yyyy-mm-dd hh:mm:ss
     *      
     * and returns as Date object
     */
    String.prototype.toDate = function () {
        var t = this.split(/[- :]/);
        var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
        return d;
    }
    
    /*
     * this function takes String in 
     *     yyyy-mm-dd hh:mm:ss
     *     
     * and returns the fuzzy elapsed time (String object)
     */    
    String.prototype.toFuzzyElapsedTime = function () {
        var a = this.toDate();
        return a.getFuzzyTimeElapsed();
    }
    
    Date.prototype.toFormattedString = function (f)
    {
        var nm = this.getMonthName();
        var nd = this.getDayName();
        var ampm = 'am';
        if ( this.getHours() > 12 ) {ampm = 'pm'};

        f = f.replace(/x/g, ampm);
        f = f.replace(/yyyy/g, this.getFullYear());
        f = f.replace(/MMM/g, nm.substr(0,3).toUpperCase());
        f = f.replace(/Mmm/g, nm.substr(0,3));
        f = f.replace(/MM\*/g, nm.toUpperCase());
        f = f.replace(/Mm\*/g, nm);
        f = f.replace(/mm/g, String(this.getMonth()+1).padLeft('0', 2));
        f = f.replace(/DDD/g, nd.substr(0,3).toUpperCase());
        f = f.replace(/Ddd/g, nd.substr(0,3));
        f = f.replace(/DD\*/g, nd.toUpperCase());
        f = f.replace(/Dd\*/g, nd);
        f = f.replace(/dd/g, String(this.getDate()).padLeft('0', 2));
        f = f.replace(/d\*/g, this.getDate());
        f = f.replace(/h/g, this.getHours() % 12);
        f = f.replace(/i/g, String(this.getMinutes()).padLeft('0', 2));
        f = f.replace(/z/g, this.getDayName());
        f = f.replace(/q/g, this.getMonthName());
        f = f.replace(/X/g, this.getSuffix());

        return f
    };
    
//    Date.prototype.getTrimmedDate = function () {
//        alert(this.getDate());
//        if(this.getDate().toString().substr(0,1)=="0") {
//            return this.getDate().toString().substr(1);
//        } else {
//            return this.getDate();
//        }
//    }

    Date.prototype.getMonthName = function () {
        switch(this.getMonth())
        {
            case 0:return 'January';
            case 1:return 'February';
            case 2:return 'March';
            case 3:return 'April';
            case 4:return 'May';
            case 5:return 'June';
            case 6:return 'July';
            case 7:return 'August';
            case 8:return 'September';
            case 9:return 'October';
            case 10:return 'November';
            case 11:return 'December';
        }
    };

    Date.prototype.getMonthString = function () {
        switch(this.getMonth())
        {
            case 0:return '01';
            case 1:return '02';
            case 2:return '03';
            case 3:return '04';
            case 4:return '05';
            case 5:return '06';
            case 6:return '07';
            case 7:return '08';
            case 8:return '09';
            case 9:return '10';
            case 10:return '11';
            case 11:return '12';
        }
    };

    Date.prototype.getSuffix = function () {
        switch(this.getDate())
        {
            case 1:
            case 21:
            case 31:
                return 'st';
            case 2:
            case 22:
                return 'nd';
            case 3:
            case 23:
                return 'rd';
            default:
                return 'th';
        }
    }

    Date.prototype.getDayName = function ()
    {
        switch(this.getDay())
        {
            case 0:return 'Sunday';
            case 1:return 'Monday';
            case 2:return 'Tuesday';
            case 3:return 'Wednesday';
            case 4:return 'Thursday';
            case 5:return 'Friday';
            case 6:return 'Saturday';
        }
    };

    String.prototype.padLeft = function (value, size)
    {
        var x = this;
        while (x.length<size) {x = value + x;}
        return x;
    };

    Date.prototype.getFuzzyTimeElapsed = function ()
    {
        // Get the current date and reference date
        var currentDate = new Date();
        var refDate = new Date(this);
        var dateOfRecord = "";

        // Extract from currentDate
        var currentYear = currentDate.getFullYear().toString();
        var currentMonth = currentDate.getMonthString();

        if (currentDate.getDate() < 10) {
            currentDay = '0' + currentDate.getDate().toString();
        } else {
            currentDay = currentDate.getDate().toString();
        }

        // Extract from refDate
        var refYear = refDate.getFullYear().toString();
        var refMonth = refDate.getMonthString();

        if (refDate.getDate() < 10) {
            refDay = '0' + refDate.getDate().toString();
        } else {
            refDay = refDate.getDate().toString();
        }
        
        // Determine the difference in time

        var tempMaxDate = currentYear + currentMonth + currentDay;
        var tempDateRef = refYear + refMonth + refDay;
        var diffInDays = parseInt(tempMaxDate) - parseInt(tempDateRef);

        if (diffInDays > 7) {
            // display regular time stamp
            dateOfRecord = refDate.toFormattedString('h:ix, z, q dX, yyyy');
        } else {
            var currentHour = currentDate.getHours();
            var currentMin = currentDate.getMinutes();
            var currentSec = currentDate.getSeconds();

            var refHour = refDate.getHours();
            var refMin = refDate.getMinutes();
            var refSec = refDate.getSeconds();

            var diffInHours = currentHour - refHour;
            var diffInMin = currentMin - refMin;
            var diffInSec = (currentSec - refSec);
            
            if (diffInSec<0) {
                diffInSec += 60;
                diffInMin --;
            }
            if (diffInMin<0) {
                diffInMin += 60;
                diffInHours --;
            }
            if (diffInHours<0) {
                diffInHours += 24;
                diffInDays --;
            }
            
            //alert(diffInDays + "days " + diffInHours + "hr " + diffInMin + "min " + diffInSec + "sec");

            // show time difference
            if (diffInDays < 1) {   // if less than a day
                if (diffInHours > 0) {
                    if (diffInMin < 0) {
                        diffInMin = 60 + diffInMin;
                        diffInHours = diffInHours - 1;
                        if (diffInHours <= 0 ) {
                            dateOfRecord = String(diffInMin) + ' min ago 1';
                        } else {
                            dateOfRecord = String(diffInHours) + ' hr ago2';// + String(diffInMin) + ' min ago';
                        }
                    } else {
                        dateOfRecord = String(diffInHours) + ' hr ago3';// + String(diffInMin) + ' min ago';
                    }
                } else {
                    if (diffInMin > 0) {
                        if (diffInSec < 0) {
                            diffInMin = diffInMin - 1;
                            diffInSec = diffInSec + 60;
                            if (diffInMin <= 0) {
                                dateOfRecord = String(diffInSec) + ' sec ago4';
                            } else {
                                dateOfRecord = String(diffInMin) + ' min ago5'; // + String(diffInSec) + ' sec ago';
                            }
                        } else {
                            dateOfRecord = String(diffInMin) + ' min ago6'; // + String(diffInSec) + ' sec ago';
                        }
                    } else {
                        dateOfRecord = String(diffInSec) + ' sec ago7';
                    }
                }
            } else {        // if more than a day
                if (diffInDays > 1) {
                    dateOfRecord = String(diffInDays) + ' days ago8';
                } else {        // if equal to a day
                    if (diffInHours < 0) {
                        diffInHours = diffInHours + 24;
                        diffInDays = diffInDays - 1;
                        if(diffInDays <= 0) {
                            dateOfRecord = String(diffInHours) + ' hr ago9';
                        } else {
                            dateOfRecord = String(diffInDays) + ' day ago10'; // + String(diffInHours) + ' hr ago';
                        }
                    } else {
                        dateOfRecord = String(diffInDays) + ' day ago11'; // + String(diffInHours) + ' hr ago';
                    }
                }
            }
        }

        return dateOfRecord;

    }

}