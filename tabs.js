// tabs
function showTab(tabName) {
    const tabs = document.getElementsByClassName('card');
    for (let i = 0; i < tabs.length; i++) {
        if (tabs[i].classList.contains(tabName)) {
            tabs[i].style.display = 'block';
        } else {
            tabs[i].style.display = 'none';
        }
    }
}

function showCategory(category) {
    const skins = document.getElementsByClassName('skinlist');
    const tablinks = document.getElementsByClassName('tablinks');

    for (let i = 0; i < skins.length; i++) {
        let defindex = skins[i].getAttribute('data-defindex');
        if (category === 'all' || getCategoryByDefindex(defindex) === category) {
            skins[i].style.display = 'block';
        } else {
            skins[i].style.display = 'none';
        }
    }

    const defaultKnifeCard = document.querySelector('.knifelist');
    if (category === 'knifes' || category === 'all') {
        if (defaultKnifeCard) {
            defaultKnifeCard.style.display = 'block';
        }
    } else {
        if (defaultKnifeCard) {
            defaultKnifeCard.style.display = 'none';
        }
    }

    for (var j = 0; j < tablinks.length; j++) {
        tablinks[j].classList.remove('active');
    }

    let clickedTab = Array.from(tablinks).find(tab => {
        let tabText = tab.textContent.toUpperCase();
        let categoryWords = category.toUpperCase().split(' ');
        return categoryWords.every(word => tabText.includes(word));
    });

    if (clickedTab) {
        clickedTab.classList.add('active');
    }

    sessionStorage.setItem('selectedCategory', category);
}

function getCategoryByDefindex(defindex) {
    if (defindex >= 500 && defindex <= 525) {
        return 'knives';
    } else if (defindex >= 1 && defindex <= 4 || defindex >= 30 && defindex <= 32 || defindex == 36 || defindex >= 61 && defindex <= 64) {
        return 'pistols';
    } else if (defindex >= 7 && defindex <= 8 || defindex == 10 || defindex == 13 || defindex == 16 || defindex == 60 || defindex == 39) {
        return 'rifles';
    } else if (defindex == 26 || defindex == 17 || defindex >= 33 && defindex <= 34 || defindex == 19 || defindex >= 23 && defindex <= 24) {
        return 'smg';
    } else if (defindex == 14 || defindex == 28) {
        return 'machine guns';
    } else if (defindex == 9 || defindex == 11 || defindex == 38 || defindex == 40) {
        return 'snipers';
    } else if (defindex == 27 || defindex == 35 || defindex == 29 || defindex == 25) {
        return 'shotguns';
    }

    return 'other';
}
