function getCompletion(id, usersList, hashtagsList, userid) {
    const tweetInput = document.getElementById(id);
    const completion = document.getElementById(userid);
    console.log(tweetInput, completion);
    if (!tweetInput) return;
    if (!completion) return;
  
    tweetInput.addEventListener("input", function() {
      if (tweetInput.value.length === 0)  {
            completion.innerHTML = "";
          return;
        }
      let text = tweetInput.value;
      const splitText = text.split(" ");
      const lastWord = splitText[splitText.length - 1];
      text = lastWord;
      const indexAt = text.lastIndexOf("@");
      const indexHashtag = text.lastIndexOf("#");
      const lastIndex = Math.max(indexAt, indexHashtag);
  
      if (lastIndex < 0) {
        completion.innerHTML = "";
        return;
      }
  
      const isUserList = indexAt > indexHashtag;
      const incompleteWord = text.substring(lastIndex + 1);
      const matchingList = isUserList ? usersList : hashtagsList;
      if (!matchingList) return;
  
      const matches = matchingList.filter(item => item.toLowerCase().startsWith(incompleteWord.toLowerCase()));
      if (matches.length === 0) return;
  
      completion.innerHTML = "";
      matches.forEach(match => {
        const item = document.createElement("span");
        const completedText = text.substring(0, lastIndex + 1) + match;
        item.textContent = completedText;
        item.addEventListener("click", function() {
            splitText[splitText.length - 1] = completedText;
            tweetInput.value = splitText.join(" ");

          completion.innerHTML = "";
        });
        item.addEventListener("keydown", function(e) {
          if (e.keyCode === 13) {
            splitText[splitText.length - 1] = completedText;
            tweetInput.value = splitText.join(" ");
            completion.innerHTML = "";
          }
        });
        item.tabIndex = "0";
        item.className = "block p-2 border border-gray-300 hover:bg-gray-300 flex-1 rounded-full cursor-pointer font-bold";
        completion.appendChild(item);
      });
    });
  }
  