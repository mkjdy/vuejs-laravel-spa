export function buildDateTime(datetime, format=null) {
    let now = new Date(datetime);
    var date = now.getDate() + '/' + (now.getMonth() + 1) + '/' + now.getFullYear();
    let hr = (now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds()).split(':');
    let h = hr[0] % 12 || 12;
    let m = parseInt(hr[1]) > 9 ? "" + parseInt(hr[1]): "0" + parseInt(hr[1]);
    let t = hr[0] > 12 ? 'PM' : 'AM';
    if (format) {
        try {
            var f_Y = now.getFullYear()
            var f_m = (now.getMonth() + 1)
            var f_d = now.getDate()
            var format_split = format.split('-')
            if (format_split.length == 3 && typeof format_split == 'object') {
                date = `${eval(`f_${format_split[0]}`)}-${eval(`f_${format_split[1]}`)}-${eval(`f_${format_split[2]}`)}`
            }
        } catch (error) {}
    }
    var formatedTime = h + ':' + m + ' ' + t;
    return date + ' ' + formatedTime ?? '00:00';
}

export function permissionsCategoryColor(category) {
    const permissions_category_color = {
        dashboard: '#32B87C',
        pr: '#3970AA',
        obr: '#A9AA55',
        user: '#5655AA',
        role: '#F56C6C',
        reports: '#C08163',
        office: '#5F7B90',
        budget: '#B47BB4',
        accountcode: '#8F847B',
        aro: '#A36163',
        budget_allotment: '#A36163',
    }

    return permissions_category_color[category]
}