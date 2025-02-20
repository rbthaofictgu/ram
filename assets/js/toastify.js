//************************************************************************/
// Envio de Mensajes Toast RTBM RBTHAOFIC@GMAIL.COM (+504)9561-4451     **/
// Fecha Creacion: 2021-08-26                                           **/
// Última Fecha Modificación:                                           **/
//************************************************************************/
function sendToast(text,duration=3000,destination='',newWindow=true,close=true,gravity="top",position="left",stopOnFocus=true,
    style={background: "linear-gradient(to right, #88cfe0, #f5e0db)",},onClick=function(){},clase='success',offset={ x: 50,  y: 10  },avatar= $appcfg_Dominio +'/assets/images/check.png') {
    console.log(3);
    if (typeof offset == Boolean && offset==false) {
        Toastify({
        text: text,
        className: clase, // `default`, `info`, `error`, `success`
        avatar: avatar,
        duration: duration,
        destination: destination,
        newWindow: newWindow,
        close: close,
        gravity: gravity, // `top` or `bottom`
        position: position, // `left`, `center` or `right`
        stopOnFocus: stopOnFocus, // Prevents dismissing of toast on hover
        style: style,
        onClick: onClick // Callback after click
        }).showToast();
    } else {
        Toastify({
            text: text,
            offset: offset,
            className: clase, // `default`, `info`, `error`, `success`
            avatar: avatar,
            duration: duration,
            destination: destination,
            newWindow: newWindow,
            close: close,
            stopOnFocus: stopOnFocus, // Prevents dismissing of toast on hover
            style: style,
            onClick: onClick // Callback after click
        }).showToast();
    }
}