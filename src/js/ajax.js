import { name_redirect } from '../../vue.config.json'

/**
 * General function to make an ajax request
 *
 * @param {String} fileName the name of the file to be requested e.g. 'getUsers.php'
 * @param {*} callback a function to be called when the request is successful
 * @param {Object} args the arguments to be passed to the file e.g. {id: 1, name: 'John'}
 * @param {String} contentType the content type of the request
 * @param {String} method the method of the request
 */
function asyncRequest(fileName, callback = () => { }, args = null, contentType = 'application/x-www-form-urlencoded', method = 'POST') {
    return new Promise((resolve,) => {
        const jsonArgs = args ? JSON.stringify(args) : null;
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                callback(JSON.parse(this.responseText));
                resolve(JSON.parse(this.responseText));
            }
        };
        xhttp.open(method, `/${name_redirect}/` + fileName, true);
        xhttp.setRequestHeader('Content-type', contentType);
        xhttp.send(jsonArgs ? 'args=' + jsonArgs : '');
    });
}

export default asyncRequest;