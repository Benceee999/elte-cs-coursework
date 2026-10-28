const defaultPlatformTypes = [
    { id: 0, name: 'Short-form content' },
    { id: 1, name: 'Közösségi média' },
    { id: 2, name: 'Televízió' },
    { id: 3, name: 'Streaming platform' }
]

const defaultPlatforms = [
    { id: 0, name: 'KitKot', color: '#fc03db', typeId: 0 },
    { id: 1, name: 'CsakVentillátorok', color: '#03dbfc', typeId: 0 },
    { id: 2, name: 'Iwiw', color: '#c7d2d4', typeId: 1 },
    { id: 3, name: 'Facetoot', color: '#0b24de', typeId: 1 },
    { id: 4, name: 'You To Be', color: '#ff001e', typeId: 1 },
    { id: 5, name: 'TVII', color: '#650b16', typeId: 2 },
    { id: 6, name: 'ELTL', color: '#5edb6d', typeId: 2 },
    { id: 7, name: 'LilaRóka', color: '#8a2be2', typeId: 3 }
]

const defaultCelebTypes = [
    { id: 0, name: 'Influenszer' },
    { id: 1, name: 'Énekes' },
    { id: 2, name: 'Színész' },
    { id: 3, name: 'Trash Celeb' }
]

const defaultCelebs = [
    { id: 0, name: 'Bajusz Patrik', platformIds: [0,3,4], celebTypeId: 0 },
    { id: 1, name: 'LordKyron', platformIds: [1,5], celebTypeId: 3 },
    { id: 2, name: 'Góth Tabi', platformIds: [1,2], celebTypeId: 0 },
    { id: 3, name: 'Ádám a Mókuscsoporttól', platformIds: [4], celebTypeId: 1 },
    { id: 4, name: 'Spárgás Laci', platformIds: [6], celebTypeId: 2 },
    { id: 5, name: 'Összebogozott Csabi', platformIds: [3,4], celebTypeId: 0 },
    { id: 6, name: 'Miller Laci', platformIds: [6], celebTypeId: 2 },
    { id: 7, name: 'Pápai Dávid', platformIds: [6, 7], celebTypeId: 1 }
]

function loadData() {
    const pt = localStorage.getItem('partyzone_platformTypes')
    const p = localStorage.getItem('partyzone_platforms')
    const ct = localStorage.getItem('partyzone_celebTypes')
    const c = localStorage.getItem('partyzone_celebs')

    platformTypes = pt ? JSON.parse(pt) : [...defaultPlatformTypes]
    platforms = p ? JSON.parse(p) : [...defaultPlatforms]
    celebTypes = ct ? JSON.parse(ct) : [...defaultCelebTypes]
    celebs = c ? JSON.parse(c) : [...defaultCelebs]
}

function saveData() {
    localStorage.setItem('partyzone_platformTypes', JSON.stringify(platformTypes))
    localStorage.setItem('partyzone_platforms', JSON.stringify(platforms))
    localStorage.setItem('partyzone_celebTypes', JSON.stringify(celebTypes))
    localStorage.setItem('partyzone_celebs', JSON.stringify(celebs))
}

let platformTypes = []
let platforms = []
let celebTypes = []
let celebs = []

loadData()

const navigationDiv = document.querySelector('#navigation')
const pages = document.querySelectorAll('.page')
const navSpans = navigationDiv.querySelectorAll('.nav-btn')

function switchPage(event) {
    const pageToShow = event.target.dataset.pageid
    for(const page of pages) {
        page.classList.add('hidden')
    }
    document.querySelector(`#${pageToShow}`).classList.remove('hidden')
    
    const currentBtn = navigationDiv.querySelector('.current')
    if (currentBtn) currentBtn.classList.remove('current')
    event.target.classList.add('current')

    if (pageToShow === 'page-statisztikak') {
        renderStats()
    }
}
for(const span of navSpans) span.onclick = switchPage

const typeListUL = document.querySelector('#type-list')
const platformListUL = document.querySelector('#platform-list')
const celebListUL = document.querySelector('#celeb-list')
const celebtypeListUL = document.querySelector('#celebtype-list')

function listPlatformTypes() {
    typeListUL.innerHTML = ''
    for (const t of platformTypes) {
        typeListUL.innerHTML += `<li>
            <span>${t.name}</span>
            <div>
                <button class="btn-edit" onclick="editType(${t.id})">Szerk</button>
                <button class="btn-delete" onclick="deleteType(${t.id})">Törlés</button>
            </div>
        </li>`
    }
    updateDropdowns()
}

function listPlatforms() {
    platformListUL.innerHTML = ''
    for (const p of platforms) {
        const pt = platformTypes.find(type => type.id == p.typeId)
        const typeName = pt ? pt.name : 'Ismeretlen'
        platformListUL.innerHTML += `<li>
            <span><span class="platform-tag" style="background-color: ${p.color}">${p.name}</span> <small>(${typeName})</small></span>
            <div>
                <button class="btn-edit" onclick="editPlatform(${p.id})">Szerk</button>
                <button class="btn-delete" onclick="deletePlatform(${p.id})">Törlés</button>
            </div>
        </li>`
    }
    updateDropdowns()
}

function listCelebTypes() {
    celebtypeListUL.innerHTML = ''
    for (const ct of celebTypes) {
        celebtypeListUL.innerHTML += `<li>
            <span>${ct.name}</span>
            <div>
                <button class="btn-edit" onclick="editCelebType(${ct.id})">Szerk</button>
                <button class="btn-delete" onclick="deleteCelebType(${ct.id})">Törlés</button>
            </div>
        </li>`
    }
    updateDropdowns()
}

function listCelebs(celebsToList) {
    celebListUL.innerHTML = ''
    for (const c of celebsToList) {
        let tagsHTML = ''
        for (const pId of c.platformIds) {
            const p = platforms.find(plat => plat.id == pId)
            if (p) {
                tagsHTML += `<span class="platform-tag" style="background-color: ${p.color}; cursor: pointer;" title="Kattints a szűréshez!" onclick="filterByPlatform(${p.id})">${p.name}</span> `
            }
        }
        const ct = celebTypes.find(type => type.id == c.celebTypeId)
        const typeName = ct ? `<span class="celeb-type-tag">${ct.name}</span>` : ''

        celebListUL.innerHTML += `<li>
            <span><strong>${c.name}</strong> ${typeName} <br><small>${tagsHTML}</small></span>
            <div>
                <button class="btn-edit" onclick="editCeleb(${c.id})">Szerk</button>
                <button class="btn-delete" onclick="deleteCeleb(${c.id})">Törlés</button>
            </div>
        </li>`
    }
}

window.filterByPlatform = function(platformId) {
    const filterSelect = document.querySelector('#filter-celeb-platform')
    filterSelect.value = `plat_${platformId}`
    filterCelebs()
}

function updateDropdowns() {
    const platformTypeSelect = document.querySelector('#platform-type')
    const filterCelebPlatformSelect = document.querySelector('#filter-celeb-platform')
    const celebPlatformsContainer = document.querySelector('#celeb-platforms-container')
    const celebTypeSelect = document.querySelector('#celeb-type')

    platformTypeSelect.innerHTML = ''
    for (const t of platformTypes) platformTypeSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`

    celebTypeSelect.innerHTML = ''
    for (const ct of celebTypes) celebTypeSelect.innerHTML += `<option value="${ct.id}">${ct.name}</option>`

    celebPlatformsContainer.innerHTML = ''
    for (const p of platforms) {
        celebPlatformsContainer.innerHTML += `<label><input type="checkbox" class="plat-cb" value="${p.id}"> <span style="color:${p.color}; font-weight:bold">${p.name}</span></label>`
    }

    const currentFilterVal = filterCelebPlatformSelect.value
    filterCelebPlatformSelect.innerHTML = `<option value="all">Mindenki</option>`
    for (const t of platformTypes) filterCelebPlatformSelect.innerHTML += `<option value="type_${t.id}">PlatformTípus: ${t.name}</option>`
    for (const p of platforms) filterCelebPlatformSelect.innerHTML += `<option value="plat_${p.id}">Platform: ${p.name}</option>`
    if (currentFilterVal) filterCelebPlatformSelect.value = currentFilterVal
}

function cancelTypeEdit() {
    document.querySelector('#type-name').value = ''
    document.querySelector('#type-id').value = ''
    document.querySelector('#type-form-title').innerText = 'Új Platformtípus'
    document.querySelector('#cancel-type-btn').classList.add('hidden')
    document.querySelector('#type-error').innerText = ''
}
document.querySelector('#cancel-type-btn').onclick = cancelTypeEdit

function cancelPlatformEdit() {
    document.querySelector('#platform-name').value = ''
    document.querySelector('#platform-color').value = '#000000'
    document.querySelector('#platform-id').value = ''
    document.querySelector('#platform-form-title').innerText = 'Új Platform'
    document.querySelector('#cancel-platform-btn').classList.add('hidden')
    document.querySelector('#platform-error').innerText = ''
}
document.querySelector('#cancel-platform-btn').onclick = cancelPlatformEdit

function cancelCelebTypeEdit() {
    document.querySelector('#celebtype-name').value = ''
    document.querySelector('#celebtype-id').value = ''
    document.querySelector('#celebtype-form-title').innerText = 'Új Celebtípus'
    document.querySelector('#cancel-celebtype-btn').classList.add('hidden')
    document.querySelector('#celebtype-error').innerText = ''
}
document.querySelector('#cancel-celebtype-btn').onclick = cancelCelebTypeEdit

function cancelCelebEdit() {
    document.querySelector('#celeb-name').value = ''
    document.querySelector('#celeb-id').value = ''
    document.querySelector('#celeb-form-title').innerText = 'Új Celeb hozzáadása'
    document.querySelector('#cancel-celeb-btn').classList.add('hidden')
    document.querySelector('#celeb-error').innerText = ''
    const checkboxes = document.querySelectorAll('.plat-cb')
    for (const cb of checkboxes) cb.checked = false
}
document.querySelector('#cancel-celeb-btn').onclick = cancelCelebEdit

document.querySelector('#save-type-btn').onclick = () => {
    const errorDiv = document.querySelector('#type-error')
    const id = document.querySelector('#type-id').value
    const name = document.querySelector('#type-name').value.trim()

    errorDiv.innerText = ''
    if (name === '') return errorDiv.innerText = 'A név kötelező!'
    if (platformTypes.some(t => t.name.toLowerCase() === name.toLowerCase() && t.id != id)) return errorDiv.innerText = 'Már létezik!'

    if (id !== '') platformTypes.find(t => t.id == id).name = name
    else platformTypes.push({ id: Date.now(), name: name })

    saveData()
    cancelTypeEdit()
    listPlatformTypes()
}

document.querySelector('#save-platform-btn').onclick = () => {
    const errorDiv = document.querySelector('#platform-error')
    const id = document.querySelector('#platform-id').value
    const name = document.querySelector('#platform-name').value.trim()
    const color = document.querySelector('#platform-color').value
    const typeId = parseInt(document.querySelector('#platform-type').value)

    errorDiv.innerText = ''
    if (name === '' || isNaN(typeId)) return errorDiv.innerText = 'Név és típus kötelező!'
    if (platforms.some(p => p.name.toLowerCase() === name.toLowerCase() && p.id != id)) return errorDiv.innerText = 'Már létezik!'

    if (id !== '') {
        const p = platforms.find(plat => plat.id == id)
        p.name = name; p.color = color; p.typeId = typeId
    } else {
        platforms.push({ id: Date.now(), name: name, color: color, typeId: typeId })
    }

    saveData()
    cancelPlatformEdit()
    listPlatforms()
}

document.querySelector('#save-celebtype-btn').onclick = () => {
    const errorDiv = document.querySelector('#celebtype-error')
    const id = document.querySelector('#celebtype-id').value
    const name = document.querySelector('#celebtype-name').value.trim()

    errorDiv.innerText = ''
    if (name === '') return errorDiv.innerText = 'A név kötelező!'
    if (celebTypes.some(ct => ct.name.toLowerCase() === name.toLowerCase() && ct.id != id)) return errorDiv.innerText = 'Már létezik ilyen nevű celebtípus!'
    
    if (id !== '') celebTypes.find(ct => ct.id == id).name = name
    else celebTypes.push({ id: Date.now(), name: name })

    saveData()
    cancelCelebTypeEdit()
    listCelebTypes()
}

document.querySelector('#save-celeb-btn').onclick = () => {
    const errorDiv = document.querySelector('#celeb-error')
    const id = document.querySelector('#celeb-id').value
    const name = document.querySelector('#celeb-name').value.trim()
    const cTypeId = parseInt(document.querySelector('#celeb-type').value)

    const checkboxes = document.querySelectorAll('.plat-cb')
    const selectedIds = []
    for (const cb of checkboxes) if (cb.checked) selectedIds.push(parseInt(cb.value))

    errorDiv.innerText = ''
    if (name === '') return errorDiv.innerText = 'A név kötelező!'
    if (selectedIds.length === 0) return errorDiv.innerText = 'Minimum 1 platform kötelező!'

    if (id !== '') {
        const c = celebs.find(celeb => celeb.id == id)
        c.name = name; c.platformIds = selectedIds; c.celebTypeId = cTypeId
    } else {
        celebs.push({ id: Date.now(), name: name, platformIds: selectedIds, celebTypeId: cTypeId })
    }

    saveData()
    cancelCelebEdit()
    filterCelebs()
}

function deleteType(id) {
    if (platforms.some(p => p.typeId == id)) {
        alert("Hiba: Nem törölhető, mert tartozik hozzá platform!")
        return
    }
    if (confirm("Biztosan törölni szeretnéd ezt a platformtípust?")) {
        platformTypes = platformTypes.filter(t => t.id != id)
        saveData()
        listPlatformTypes()
    }
}

function deletePlatform(id) {
    if (celebs.some(c => c.platformIds.includes(id))) {
        alert("Hiba: Nem törölhető, mert van celeb, aki használja!")
        return
    }
    if (confirm("Biztosan törölni szeretnéd ezt a platformot?")) {
        platforms = platforms.filter(p => p.id != id)
        saveData()
        listPlatforms()
    }
}

function deleteCelebType(id) {
    if (celebs.some(c => c.celebTypeId == id)) {
        alert("Hiba: Nem törölhető, mert van ilyen típusú celeb!")
        return
    }
    if (confirm("Biztosan törölni szeretnéd ezt a celebtípust?")) {
        celebTypes = celebTypes.filter(ct => ct.id != id)
        saveData()
        listCelebTypes()
    }
}

function deleteCeleb(id) {
    if (confirm("Biztosan törölni szeretnéd ezt a celebet?")) {
        celebs = celebs.filter(c => c.id != id)
        saveData()
        filterCelebs()
    }
}

function editType(id) {
    const t = platformTypes.find(type => type.id == id)
    document.querySelector('#type-id').value = t.id
    document.querySelector('#type-name').value = t.name
    
    const title = document.querySelector('#type-form-title')
    title.innerText = 'Típus szerkesztése'
    document.querySelector('#cancel-type-btn').classList.remove('hidden')
    title.scrollIntoView({ behavior: 'smooth' })
}

function editPlatform(id) {
    const p = platforms.find(plat => plat.id == id)
    document.querySelector('#platform-id').value = p.id
    document.querySelector('#platform-name').value = p.name
    document.querySelector('#platform-color').value = p.color
    document.querySelector('#platform-type').value = p.typeId
    
    const title = document.querySelector('#platform-form-title')
    title.innerText = 'Platform szerkesztése'
    document.querySelector('#cancel-platform-btn').classList.remove('hidden')
    title.scrollIntoView({ behavior: 'smooth' })
}

function editCelebType(id) {
    const ct = celebTypes.find(type => type.id == id)
    document.querySelector('#celebtype-id').value = ct.id
    document.querySelector('#celebtype-name').value = ct.name
    
    const title = document.querySelector('#celebtype-form-title')
    title.innerText = 'Celebtípus szerkesztése'
    document.querySelector('#cancel-celebtype-btn').classList.remove('hidden')
    title.scrollIntoView({ behavior: 'smooth' })
}

function editCeleb(id) {
    const c = celebs.find(celeb => celeb.id == id)
    document.querySelector('#celeb-id').value = c.id
    document.querySelector('#celeb-name').value = c.name
    document.querySelector('#celeb-type').value = c.celebTypeId
    
    const checkboxes = document.querySelectorAll('.plat-cb')
    for (const cb of checkboxes) {
        cb.checked = c.platformIds.includes(parseInt(cb.value))
    }

    const title = document.querySelector('#celeb-form-title')
    title.innerText = 'Celeb szerkesztése'
    document.querySelector('#cancel-celeb-btn').classList.remove('hidden')
    title.scrollIntoView({ behavior: 'smooth' })
}

document.querySelector('#filter-celeb-btn').onclick = filterCelebs

function filterCelebs() {
    const nameFilterValue = document.querySelector('#filter-celeb-name').value.toLowerCase()
    const platformFilterValue = document.querySelector('#filter-celeb-platform').value

    let filteredCelebs = celebs.filter(c => c.name.toLowerCase().includes(nameFilterValue))

    if (platformFilterValue !== 'all') {
        const parts = platformFilterValue.split('_')
        const filterKind = parts[0]
        const filterId = parseInt(parts[1])

        if (filterKind === 'plat') {
            filteredCelebs = filteredCelebs.filter(c => c.platformIds.includes(filterId))
        } else if (filterKind === 'type') {
            const typePlatformIds = platforms.filter(p => p.typeId == filterId).map(p => p.id)
            filteredCelebs = filteredCelebs.filter(c => c.platformIds.some(id => typePlatformIds.includes(id)))
        }
    }
    listCelebs(filteredCelebs)
}

let sortStates = {
    stat1: { column: null, asc: true },
    stat2: { column: null, asc: true },
    stat3: { column: null, asc: true },
    stat4: { column: null, asc: true }
}

window.sortStat = function(statId, column) {
    let state = sortStates[statId]
    if (state.column === column) {
        if (state.asc) {
            state.asc = false 
        } else {
            state.column = null 
            state.asc = true
        }
    } else {
        state.column = column 
        state.asc = true
    }
    renderStats() 
}

function applySort(dataArray, state) {
    if (!state.column) return dataArray 
    
    let sorted = [...dataArray] 
    sorted.sort((a, b) => {
        let valA = a[state.column]
        let valB = b[state.column]
        
        if (valA < valB) return state.asc ? -1 : 1
        if (valA > valB) return state.asc ? 1 : -1
        return 0
    })
    return sorted
}

function renderStats() {
    let data1 = []
    for (const p of platforms) {
        const count = celebs.filter(c => c.platformIds.includes(p.id)).length
        data1.push({ name: p.name, count: count, color: p.color })
    }

    let data2 = []
    for (const t of platformTypes) {
        const typePlatformIds = platforms.filter(p => p.typeId == t.id).map(p => p.id)
        const count = celebs.filter(c => c.platformIds.some(id => typePlatformIds.includes(id))).length
        data2.push({ name: t.name, count: count, color: '#555' })
    }

    let data3 = []
    for (const t of platformTypes) {
        const count = platforms.filter(p => p.typeId == t.id).length
        data3.push({ name: t.name, count: count, color: '#17a2b8' })
    }

    let data4 = [] 
    for (const ct of celebTypes) {
        const count = celebs.filter(c => c.celebTypeId == ct.id).length
        data4.push({ name: ct.name, count: count, color: '#ffc107' })
    }

    data1 = applySort(data1, sortStates.stat1)
    data2 = applySort(data2, sortStates.stat2)
    data3 = applySort(data3, sortStates.stat3)
    data4 = applySort(data4, sortStates.stat4)

    const tbody1 = document.querySelector('#stat-body-1')
    tbody1.innerHTML = ''
    for (const row of data1) tbody1.innerHTML += `<tr><td>${row.name}</td><td>${row.count} db</td><td style="color:${row.color}">${'█'.repeat(row.count)}</td></tr>`

    const tbody2 = document.querySelector('#stat-body-2')
    tbody2.innerHTML = ''
    for (const row of data2) tbody2.innerHTML += `<tr><td>${row.name}</td><td>${row.count} db</td><td style="color:${row.color}">${'█'.repeat(row.count)}</td></tr>`

    const tbody3 = document.querySelector('#stat-body-3')
    tbody3.innerHTML = ''
    for (const row of data3) tbody3.innerHTML += `<tr><td>${row.name}</td><td>${row.count} db</td><td style="color:${row.color}">${'█'.repeat(row.count)}</td></tr>`

    const tbody4 = document.querySelector('#stat-body-4')
    tbody4.innerHTML = ''
    for (const row of data4) tbody4.innerHTML += `<tr><td>${row.name}</td><td>${row.count} db</td><td style="color:${row.color}">${'█'.repeat(row.count)}</td></tr>`
}

listPlatformTypes()
listPlatforms()
listCelebTypes()
listCelebs(celebs)