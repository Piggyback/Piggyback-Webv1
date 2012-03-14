/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function test() {
    jQuery.post('test/test_parse', function(data) {
        alert(data);
    });
}