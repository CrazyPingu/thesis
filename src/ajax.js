function asyncRequest(fileName, callback = () => { }, args = null, contentType = 'application/x-www-form-urlencoded') {
    return new Promise((resolve, ) => {
        const jsonArgs = args ? JSON.stringify(args) : null;
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                callback(JSON.parse(this.responseText));
                resolve(JSON.parse(this.responseText));
            }
        };
        xhttp.open('POST', 'requests/' + fileName, true);
        xhttp.setRequestHeader('Content-type', contentType);
        xhttp.send(jsonArgs ? 'args=' + jsonArgs : '');
    });
}

export default asyncRequest;