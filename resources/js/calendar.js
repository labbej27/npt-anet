// resources/js/calendar.js
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'
import frLocale from '@fullcalendar/core/locales/fr'

// ⚠️ V6 n'exige plus d'importer des .css : le style est injecté automatiquement par JS.
// → Ne pas importer '@fullcalendar/*/index.css'

function qs(id){ return document.getElementById(id) }

window.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('calendar')
  if (!el) return

  const calendar = new Calendar(el, {
    plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
    initialView: 'dayGridMonth',
    firstDay: 1,
    locale: frLocale,
    height: 'auto',
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
    events: '/api/calendar-events',

    eventClick: (info) => {
      info.jsEvent.preventDefault()

      const d = info.event
      const p = d.extendedProps || {}
      const dialog = qs('reserveDialog')
      const form   = qs('reserveForm')
      const infos  = qs('reserveInfos')

      // Reset éventuel (au cas où une modale précédente a laissé des valeurs)
      if (form) form.reset()

      // Action vers /sessions/{id}/reserver
      form.action = `/sessions/${d.id}/reserver`

      // Message d'informations (avec note double opt-in)
      const dateLabel = d.start.toLocaleDateString('fr-FR', { weekday:'long', day:'2-digit', month:'long', year:'numeric' })
      const note = `<div class="mt-2 text-xs text-amber-700">
        ⚠️ Votre place sera <strong>confirmée après clic</strong> sur le lien reçu par e-mail.
      </div>`

      infos.innerHTML = `
        <div><strong>${p.topic || 'Atelier'}</strong></div>
        <div>${dateLabel}</div>
        <div>${p.start_h} – ${p.end_h} • ${p.location || ''}</div>
        <div>Places restantes : <strong>${p.spotsLeft}</strong></div>
        ${note}
      `

      // Gérer le cas "complet" directement dans la modale
      const submitBtn = form.querySelector('button') // ton bouton "Confirmer la réservation"
      const inputs = Array.from(form.querySelectorAll('input'))

      if (p.spotsLeft <= 0) {
        if (submitBtn) {
          submitBtn.disabled = true
          submitBtn.textContent = 'Complet'
          submitBtn.classList.add('opacity-60', 'cursor-not-allowed')
        }
        inputs.forEach(i => i.disabled = true)
      } else {
        if (submitBtn) {
          submitBtn.disabled = false
          submitBtn.textContent = 'Confirmer la réservation'
          submitBtn.classList.remove('opacity-60', 'cursor-not-allowed')
        }
        inputs.forEach(i => i.disabled = false)
      }

      // Anti double-clic submit côté UI
      form.addEventListener('submit', () => {
        if (submitBtn) {
          submitBtn.disabled = true
          submitBtn.textContent = 'Envoi...'
        }
      }, { once: true })

      if (typeof dialog.showModal === 'function') dialog.showModal()
      else dialog.setAttribute('open','')
    },

    eventDidMount: (arg) => {
      // Ajouter un title natif
      const p = arg.event.extendedProps || {}
      arg.el.title = `${p.topic} — ${p.start_h}-${p.end_h} — Places: ${p.spotsLeft}`
    },
  })

  calendar.render()
})
