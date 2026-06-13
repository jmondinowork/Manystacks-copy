import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

export const formattedDateHour = (date) => { if (date) return format(new Date(date), 'dd MMM yyyy, HH:mm', { locale: fr }) };
export const formattedDate = (date) => { if (date) return format(new Date(date), 'dd/MM/yyyy', { locale: fr }) };

export const downloadImage = (imageUrl, imageName) => {
    fetch(imageUrl)
        .then(response => response.blob())
        .then(blob => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = imageName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        })
        .catch(e => console.error(e));
}

export const slugify = (str) => {
    let slug = str
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, "")
        .toLowerCase()
        .replace(/[\s\W-]+/g, '-');

    if (slug.endsWith('-')) {
        slug = slug.slice(0, -1);
    }

    return slug;
}

export const userInitials = (username) => {
    const splitName = username.split(' ');
    return (splitName[0][0] + splitName[splitName.length - 1][0]).toUpperCase();
};

export const copyToClipboard = (text) => {
    if (!text) return;

    navigator.clipboard.writeText(text);
};

export const navigateTo = (url) => {
    window.location.href = url;
}

export const pastDate = (date) => {
    return new Date(date) < new Date();
}

export const objectToFormData = (obj, formData = new FormData(), parentKey = '') => {
    if (obj && typeof obj === 'object' && !(obj instanceof File)) {
        Object.keys(obj).forEach(key => {
            const value = obj[key];
            // Construit la clé en gérant la hiérarchie
            const formKey = parentKey ? `${parentKey}[${key}]` : key;

            if (value === null || value === undefined || value === '') {
                return;
            }

            if (Array.isArray(value)) {
                value.forEach((item, index) => {
                    // Si l'élément du tableau est un objet (et pas un File), on fait de la récursion
                    if (typeof item === 'object' && item !== null && !(item instanceof File)) {
                        objectToFormData(item, formData, `${formKey}[${index}]`);
                    } else {
                        formData.append(`${formKey}[${index}]`, item);
                    }
                });
            } else if (value && typeof value === 'object') {
                // Recursion pour les objets imbriqués
                objectToFormData(value, formData, formKey);
            } else {
                // Pour les valeurs primitives et les fichiers
                formData.append(formKey, value);
            }
        });
    } else if (parentKey) {
        // Cas où obj est une valeur primitive ou un File, associé à une clé parent
        formData.append(parentKey, obj);
    }

    return formData;
}

