// resources/js/Filters/DateFilter.js
export default {
  install(app) {
    app.config.globalProperties.$filters = {
      /**
       * Formats a date-time string. Handles formats like "2026-03-19T00:00:00.000000Z 22:41:00"
       * by combining the date from the ISO part and the time from the separate time part.
       * If only an ISO string is provided, it formats both date and time from it.
       * @param {string} value The date-time string to format.
       * @returns {string} The formatted date-time string.
       */
      formatDateTime(value) {
        if (!value) return '';

        const parts = value.split(' ');
        const isoDatePart = parts[0]; // e.g., "2026-03-19T00:00:00.000000Z"
        const timePart = parts[1];    // e.g., "22:41:00"

        try {
          const date = new Date(isoDatePart);
          if (isNaN(date.getTime())) {
            return value; // Return original if the ISO part is not a valid date
          }

          const formattedDate = new Intl.DateTimeFormat('pl-PL', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
          }).format(date);

          if (timePart) {
            return `${formattedDate} ${timePart}`;
          } else {
            // If there's no separate time part, format the full date and time from the ISO string
            return new Intl.DateTimeFormat('pl-PL', {
              year: 'numeric',
              month: '2-digit',
              day: '2-digit',
              hour: '2-digit',
              minute: '2-digit',
              hour12: false,
            }).format(date);
          }
        } catch (e) {
          return value; // Return original on any error during parsing/formatting
        }
      },

      /**
       * Formats a date string to "YYYY-MM-DD" (locale-specific).
       * @param {string} value The date string to format.
       * @returns {string} The formatted date string.
       */
      formatDate(value) {
        if (!value) return '';
        try {
          const date = new Date(value);
          if (isNaN(date.getTime())) {
            return value;
          }
          return new Intl.DateTimeFormat('pl-PL', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
          }).format(date);
        } catch (e) {
          return value;
        }
      }
    };
  }
};
