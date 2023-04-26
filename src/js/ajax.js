import vueConfig from '../../vue.config.json';
/**
 * Sends an asynchronous XMLHttpRequest with optional arguments and a callback function.
 *
 * @param {string} fileName - The name of the file to request.
 * @param {function} [callback=() => {}] - An optional callback function to execute on successful response.
 * @param {any} [args=null] - An optional argument to pass in the request.
 * @param {string} [method='POST'] - The HTTP method to use for the request.
 * @param {string} [contentType='application/x-www-form-urlencoded'] - The content type of the request.
 *
 */
function asyncRequest(fileName, callback = () => {}, args = null, method = 'POST', contentType = 'application/x-www-form-urlencoded') {
  return new Promise((resolve) => {
    const { name_redirect } = vueConfig;
    const jsonArgs = args ? JSON.stringify(args) : null;
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
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
