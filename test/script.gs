function doPost(e) {
    var error = null;
    if (typeof e.parameter.source === "undefined") {
        error = "source is required";
    } else if (typeof e.parameter.target === "undefined") {
        error = "target is required";
    } else if (typeof e.parameter.text === "undefined") {
        error = "text is required";
    } else if (typeof e.parameter.source.trim() === "") {
        error = "source is required";
    } else if (typeof e.parameter.target.trim() === "") {
        error = "target is required";
    } else if (typeof e.parameter.text.trim() === "") {
        error = "text is required";
    } else {
        var source = e.parameter.source.trim();
        var target = e.parameter.target.trim();
        var text = e.parameter.text.trim();

        if(source === "auto"){
            source = "";
        }
        try{
            var laguage = LanguageApp.translate(text, source, target);
        }catch (err){
            error = err.meesage;
        }
    }
    if(error === null){
        var result = JSON.stringify(
            {
                "status": "success",
                "translate": laguage
            }
        )}else{
        var result = JSON.stringify({
            "status": "error",
            "error": error
        })
    }
    return ContentService.createTextOutput(result);
}