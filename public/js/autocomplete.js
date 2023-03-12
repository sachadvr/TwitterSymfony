function getCompletion(id, list, userid) {
    if (list == null) return;
    var new_tweet = document.getElementById(id);
    console.log(new_tweet);
    var test = list;
    new_tweet.addEventListener("input", function() {
        var text = new_tweet.value;
        var regex = /@[\w-]+/g; 
        var match = null;
        var lastIndex = -1;
        while ((match = regex.exec(text)) !== null) {
            lastIndex = match.index;
        }
        var lastword = text.split(" ");
        if (lastword[lastword.length - 1].indexOf("@") != 0) {
            var completion = document.getElementById(userid);
            completion.innerHTML = "";
            return;
        }

        if (lastIndex >= 0) { 
            var index2 = text.indexOf(" ", lastIndex); 
            if (index2 == -1) { 
                index2 = text.length;
            }
            var username = text.substring(lastIndex + 1, index2); 
            if (username.length > 0) {
                var completion = document.getElementById(userid);
                completion.innerHTML = "";
                for (var i = 0; i < test.length; i++) {
                    if (test[i].indexOf(username) == 0) {
                        var user = document.createElement("span");
                        
                        user.innerHTML = test[i];
                        user.addEventListener("click", function() {
                            document.getElementById(id).value = text.substring(0, lastIndex + 1) + this.innerHTML + " ";
                            completion.innerHTML = "";
                        });
                        // same for enter
                        user.addEventListener("keydown", function(e) {
                            if (e.keyCode == 13) {
                                document.getElementById(id).value = text.substring(0, lastIndex + 1) + this.innerHTML + " ";
                                completion.innerHTML = "";
                            }
                        });
                        
                        user.tabIndex = "0";
                        user.className = "block p-2 border border-gray-300 hover:bg-gray-300 flex-1 rounded-full cursor-pointer font-bold";
                        completion.appendChild(user);
                    }
                }
            }
        } else {
            var completion = document.getElementById(userid);
            completion.innerHTML = ""; 
        }
    });
}