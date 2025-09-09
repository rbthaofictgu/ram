//************************************************************************/
// Envio de Mensajes Toast RTBM RBTHAOFIC@GMAIL.COM (+504)9561-4451     **/
// Fecha Creacion: 2021-08-26                                           **/
// Última Fecha Modificación:                                           **/
//************************************************************************/
function sendToast(text,duration=5000,destination='',newWindow=true,close=true,gravity="top",position="center",stopOnFocus=true,
    style={background: "linear-gradient(to right, #88cfe0, #f5e0db)", border: "3px solid #88cfe0",borderRadius: "8px",color: "#949699",},
    onClick=function(){},clase='success',offset=false,avatar= $appcfg_Dominio +'/assets/images/check.png') {
    //* offset={ x: 50,  y: 10  } *//
    console.log('sendToast called with parameters:');
    console.log('offset', offset);
    console.log('position', position);
    console.log('typeof offset == Boolean', typeof offset == Boolean);
    if (offset==false || offset=='false') {
        console.log('offset is false, using default offset');
        console.log('style is', style);
        Toastify({
        text: text,
        escapeMarkup: false,
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
            escapeMarkup: false,
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