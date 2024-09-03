const apakahPrima = (angka) => {
    let pembagi = 0;
    for (let i = 1; i <= angka; i++) {
        if (angka % i == 0) {
            pembagi++
        }
    }
    if (pembagi == 2) {
        console.log(angka)
    } else {
        console.log("bukan prima")
    }
}

for (let i = 1; i <= 100; i++) {
    apakahPrima(i); //prima 
}