import moment from "moment-timezone";
import Vue from "vue";
import optional from "./Optional";

const settings = window.settings || {};

moment.locale(window.appLanguage)

export const date_format = () => {
    return {
        'd-m-Y': 'DD-MM-YYYY',
        'm-d-Y': 'MM-DD-YYYY',
        'Y-m-d': 'YYYY-MM-DD',
        'm/d/Y': 'MM/DD/YYYY',
        'd/m/Y': 'DD/MM/YYYY',
        'Y/m/d': 'YYYY/MM/DD',
        'm.d.Y': 'MM.DD.YYYY',
        'd.m.Y': 'DD.MM.YYYY',
        'Y.m.d': 'YYYY.MM.DD',
    };
};

export const localTimeZone = settings.timezone_preference === 'fixed' ? settings.time_zone : moment.tz.guess();

moment.tz.setDefault(localTimeZone);

export const serverDateTimeFormat = 'YYYY-MM-DD H:mm:ss';

export const serverDateFormat = 'YYYY-MM-DD';

export const serverTimeFormat = 'H:mm:ss';

export const formatted_date = () => {
    return date_format()[optional(settings, 'date_format')] || date_format()['d-m-Y'];
};

export const formatted_time = () => {
    return optional(settings, 'time_format') === 'h' ? '12' : '24';
}

export const time_format = () => {
    const format = optional(settings, 'time_format');

    const time_format = settings.time_format || 'H'

    return format === 'h' ? `${settings.time_format}:mm A` : `${time_format}:mm`;
}

export const formattedDate = (date) => {
    if (!date)
        return '';

    return moment(date, serverDateFormat, localTimeZone)
        .format(formatted_date());
};

export const formatDateToLocal = (date, withTime = false) => {
    if (!date)
        return '';
    const formatString = withTime ? `${formatted_date()} ${time_format()}` : formatted_date();

    return moment.utc(date, withTime ? serverDateTimeFormat : serverDateFormat)
        .tz(localTimeZone)
        .format(formatString);
};

export const formattedLocalTime = (withDate = false) => {
    const formatString = withDate ? `${formatted_date()} ${time_format()}` : time_format();

    return moment().format(formatString);
};

export const timezoneDate = () => {
    return moment().format(serverDateFormat);
};

export const formatDateTimeToLocalDate = (dateTime) => {
    if (!dateTime)
        return '';

    return moment.utc(dateTime, serverDateTimeFormat)
        .tz(localTimeZone)
        .format(formatted_date());
};

export const dateTimeToLocalWithFormat = (date = null) => {
    if (!date) {
        return '';
    }

    return moment.utc(date, serverDateTimeFormat)
        .tz(localTimeZone)
        .format(serverDateTimeFormat)
}

export const timeInterval = (date) => {
    return moment(date).utc(false).fromNow();
};

export const onlyTime = date => {
    return moment.utc(date, serverDateTimeFormat)
        .tz(localTimeZone)
        .format(time_format());
};

export const formatUtcToLocal = (time = null, format = false) => {
    if (!time)
        return null;

    return moment.utc(time, serverTimeFormat).tz(localTimeZone).format(format || time_format());
}

export const isValidDate = string => {
    if (!string)
        return false;

    if (typeof parseInt(string) === 'number' && string.split('-').length <= 1)
        return false;

    return !isNaN(new Date().getTime());
}

export const calenderTime = (date, withTime = true, needTimeZoneConversion = false) => {
    date = withTime ? moment(formatDateToLocal(date, true), `${formatted_date()} ${time_format()}`) : date;

    if (moment(date).format('YYYY') < moment().format('YYYY')) {
        if (needTimeZoneConversion){
            return moment(formatDateToLocal(date, true), `${formatted_date()} ${time_format()}`).format(formatted_date());
        }
        return formattedDate(date)
    }

    const days = moment(date).diff(moment.now(), 'days');
    if (days < -1 || days > 1) {
        if (needTimeZoneConversion){
            return moment(formatDateToLocal(date, true), `${formatted_date()} ${time_format()}`).format('DD MMM, YY');
        }
        return moment(date, serverDateFormat, localTimeZone).format('DD MMM, YY')
    }


    // nextWeek: '[' + Vue.prototype.$t('next_week') + ']',
    // lastWeek: '[' + Vue.prototype.$t('last') + '] dddd',

    moment.locale(window.appLanguage, {})
    let format = {
        sameDay: `[${Vue.prototype.$t('today')}] ${withTime ? '[at] LT' : ''}`,
        lastDay: `[${Vue.prototype.$t('yesterday')}] ${withTime ? '[at] LT' : ''}`,
        nextDay: `[${Vue.prototype.$t('tomorrow')}] ${withTime ? '[at] LT' : ''}`,
        nextWeek: `DD MMM, YY ${withTime ? '[at] LT' : ''}`,
        lastWeek: `DD MMM, YY ${withTime ? '[at] LT' : ''}`,
        sameElse: `${date_format()[settings.date_format]}`
    };
    if (withTime) {
        return date.calendar(format);
    }
    if (needTimeZoneConversion){
        return moment(formatDateToLocal(date, true), `${formatted_date()} ${time_format()}`).calendar(null, format);
    }
    return moment(date).calendar(null, format);
};


export const localToUtc = (time = null) => {
    if (!time) {
        return '';
    }

    moment.locale('en');
    return moment(time, time_format()).utc().format('HH:mm')
}

export const formatDateForServer = (date = null) => {
    if (!date) {
        return '';
    }

    // return moment(moment(date), formatted_date()).format(serverDateFormat);
    return moment(date, serverDateTimeFormat, localTimeZone).format(serverDateFormat);
}

export const formatTimeForServer = (time = null) => {
    if (!time) {
        return '';
    }

    moment.locale('en');
    return moment(time, time_format()).format(serverTimeFormat);
}

export const today = () => {
    return moment(new Date()).tz(localTimeZone);
}

export const thisWeek = () => {
    return [
        moment().tz(localTimeZone).startOf('week'),
        moment().tz(localTimeZone).endOf('week')
    ];
}

export const lastWeek = () => {
    return [
        moment().tz(localTimeZone).subtract(1, 'weeks').startOf('week'),
        moment().tz(localTimeZone).subtract(1, 'weeks').endOf('week')
    ];
}

export const thisMonth = () => {
    return [
        moment().tz(localTimeZone).startOf('month'),
        moment().tz(localTimeZone).endOf('month')
    ]
}

export const lastMonth = () => {
    return [
        moment().tz(localTimeZone).subtract(1, 'month').startOf('month'),
        moment().tz(localTimeZone).subtract(1, 'month').endOf('month')
    ]
}

export const thisYear = () => {
    return [
        moment(new Date()).tz(localTimeZone).startOf('year'),
        moment(new Date()).tz(localTimeZone).endOf('year'),
    ]
}

export const lastYear = () => {
    return [
        moment(new Date()).tz(localTimeZone).subtract(1, 'year').startOf('year'),
        moment(new Date()).tz(localTimeZone).subtract(1, 'year').endOf('year'),
    ]
}

export const total = () => {
    return [
        'total'
    ]
}

export const startAndEndOf = (year, month) => {
    return [
        moment([year, month]).tz(localTimeZone).startOf('month'),
        moment([year, month]).tz(localTimeZone).endOf('month')
    ]
}

export const getDateRange = (type) => {
    return {
        today,
        thisWeek,
        lastWeek,
        thisMonth,
        lastMonth,
        thisYear,
        lastYear,
        total,
    }[type]();
}

export const differentInTime = (startTime, endTime, withDate = false) => {
    if (withDate) {
        return moment.duration(moment(endTime, serverDateTimeFormat).diff(moment(startTime, serverDateTimeFormat)));
    }

    if (moment(endTime, serverTimeFormat).diff(moment(startTime, serverTimeFormat), 'hours') < 0) {
        startTime = moment(`${today().format(serverDateFormat)} ${startTime}`);
        endTime = moment(`${today().add(1, 'day').format(serverDateFormat)} ${endTime}`)
    }

    return moment.duration(moment(endTime, serverTimeFormat).diff(moment(startTime, serverTimeFormat)));
}

export const getDifferentTillNow = (startTime, endTime = moment.now()) => {
    return differentInTime(dateTimeToLocalWithFormat(startTime), moment(endTime).format(serverDateTimeFormat), true);
}

export const convertSecondToHourMinutes = (seconds, withSeconds = false) => {
    const min = parseInt(parseInt(seconds) / 60);
    const hour = min / 60;
    const absHour = parseInt(hour);
    const absMin = Math.abs(min - (absHour * 60));
    const sec = parseInt(parseInt(seconds) % 60);

    let timeFormat = `${String(absHour).length === 1 ? `0${absHour}` : absHour}:${String(absMin).length === 1 ? `0${absMin}` : String(absMin).substr(0, 2)}${withSeconds ? `:${String(sec).length === 1 ? `0${sec}` : sec}` : ''}`;

    return (absHour === 0 && (min - (absHour * 60)) < 0) ? `-${timeFormat}` : timeFormat;
}

export const dateTimeFormat = (value) => {
    if (value) {
        return `${onlyTime(value)}, ${calenderTime(value, false, true)}`
    }
    return null;
};

export const timeToDateTime = (date, time) => {
    return moment(`${moment(date, serverDateFormat).format(serverDateFormat)} ${time}`);
}

export const formatDateTimeForServer = (dateTime = null) => {
    if (!dateTime) {
        return '';
    }
    // return moment(dateTime, `${formatted_date()} ${time_format()}`).utc().format(serverDateTimeFormat)
    console.log(dateTime, moment(dateTime, serverDateTimeFormat).utc().format(serverDateTimeFormat))
    return moment(dateTime, serverDateTimeFormat).utc().format(serverDateTimeFormat)
}

export const isAfterNow = (value) => {
    return moment(value, serverDateFormat).isAfter(moment.now());
}

export const isSameOrAfterThisYear = (value) => {
    return moment(value).isSameOrAfter(moment.now(), "year");
}

export const getUtcToLocalTimeOnly = (dateTime) => {
    return moment.utc(dateTime, serverDateTimeFormat)
        .tz(localTimeZone)
        .format(serverTimeFormat);
}

export const calenderTimeWithMonthSortName = (date) => {
    return moment(date).format('DD MMM, YY')
}

export const getHoursAndMinutesInString = (seconds, short = false, roundedTo = 'abs') => {
    let min = Math.abs(parseInt(seconds) / 60);

    if (roundedTo === 'ceil') {
        min = Math.ceil(parseInt(seconds) / 60);
    }

    const hour = min / 60;
    const absHour = parseInt(hour);
    const absMin = Math.abs(min - (absHour * 60));
    let H = 'hours'
    let h = 'hour'
    let M = 'minutes'
    let m = 'minute'
    if (short) {
        h = 'h';
        H = 'h';
        m = 'm';
        M = 'm';
    }
    if (absHour > 0) {
        return `${absHour} ${absHour > 1 ? H : h} 
        ${absMin > 0 ? `and ${String(absMin).substr(0, 2)} ${absMin > 1 ? M : m}` : ''}`;
    }
    return `${String(absMin).substr(0, 2)} ${absMin > 1 ? M : m}`;
}

export const getGreetingTime = (currentTime = null) => {
    if (currentTime) {
        currentTime = moment(currentTime);
    } else {
        currentTime = moment();
    }
    const splitAfternoon = 12; // 24hr time to split the afternoon
    const splitEvening = 18; // 24hr time to split the evening
    const splitMorning = 4; // 24hr time to split the evening
    const currentHour = parseFloat(currentTime.format('HH'));

    if (currentHour >= splitMorning && currentHour <= splitAfternoon) {
        // Between 4 AM and 12PM
        return Vue.prototype.$t('good_morning');
    } else if (currentHour >= splitAfternoon && currentHour <= splitEvening) {
        // Between 12 PM and 5PM
        return Vue.prototype.$t('good_afternoon');
    }
    // Between dawn and noon
    return Vue.prototype.$t('good_evening');
}

export const getDateDifferenceString = (start, end) => {
    let startMonthNo = moment(start).month();
    let endMonthNo = moment(end).month();

    let startFormat = moment(start).date();
    let endFormat = moment(end).format('D MMM YYYY');
    if (startMonthNo !== endMonthNo) {
        startFormat = moment(start).format('D MMM');
    }

    return `${startFormat} - ${endFormat}`
}

export const customDateFormat = (date, format = 'D MMM, YYYY') => {
    return moment(date).format(format)
}

export const getSecondFromTime = (time = null) => {
    if (!time)
        return null;

    return moment.duration(time).asSeconds();
}

export const getDurationFromTime = (time = null) => {
    if (!time)
        return null;

    return getHoursAndMinutesInString(getSecondFromTime(time), true);
}