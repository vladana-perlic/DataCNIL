# -*- coding: utf-8 -*-

import requests
import os
import tarfile
from bs4 import BeautifulSoup
import re
import shutil


# Fonction pour télécharger des fichiers à partir d'une URL
def download_file(url, local_filename):
    with requests.get(url, stream=True) as r:
        r.raise_for_status()
        with open(local_filename, 'wb') as f:
            for chunk in r.iter_content(chunk_size=8192):
                f.write(chunk)
    return local_filename

# Fonction pour extraire les fichiers tar.gz
def extract_tar_gz(file_path, extract_path):
    with tarfile.open(file_path, 'r:gz') as tar:
        tar.extractall(path=extract_path)

# Fonction pour déplacer uniquement les fichiers des sous-répertoires vers un répertoire cible
def move_files(source_dir, target_dir):
    for root, dirs, files in os.walk(source_dir):
        for file in files:
            source_file_path = os.path.join(root, file)
            if not os.path.isdir(source_file_path):  # Vérifier si ce n'est pas un répertoire
                shutil.move(source_file_path, target_dir)

# URL du site
base_url = "https://echanges.dila.gouv.fr/OPENDATA/CNIL/"
download_folder = "./dataxml/cnil/"
extracted_folder = "./extracted/"
file_items_file = "file_items.txt"

# Créer les dossiers de téléchargement et d'extraction s'ils n'existent pas
if not os.path.exists(download_folder):
    os.makedirs(download_folder)

if not os.path.exists(extracted_folder):
    os.makedirs(extracted_folder)

# Charger les éléments de fichier existants à partir de file_items.txt s'il existe
existing_file_items = set()
if os.path.exists(file_items_file):
    with open(file_items_file, 'r') as f:
        existing_file_items = set(f.read().splitlines())

# Obtenir le contenu de la page
response = requests.get(base_url + "?C=M;O=D")
soup = BeautifulSoup(response.content, 'html.parser')

# Trouver tous les liens
file_items = soup.find_all('a', href=re.compile('\.tar\.gz$'))

# Ensemble pour stocker de nouveaux éléments de fichier
new_file_items = set()

# Parcourir les fichiers et vérifier s'ils sont nouveaux
for item in file_items:
    file_link = item.get('href')
    new_file_items.add(file_link)

# Comparer les nouveaux éléments de fichier avec les existants
new_files = new_file_items - existing_file_items

# Traiter les nouveaux fichiers
for file_link in new_files:
    download_url = base_url + file_link
    file_path = os.path.join(download_folder, file_link)

    # Télécharger le fichier
    print("Telechargement :", download_url)
    download_file(download_url, file_path)

    # Extraire le fichier
    print("Extraction :", file_link)
    extract_tar_gz(file_path, extracted_folder)

    # Déplacer les fichiers du dossier extrait vers le dossier de téléchargement
    print("Deplacement vers ./dataxml/cnil/")
    move_files(extracted_folder, download_folder)

# Mettre à jour file_items.txt avec les nouveaux éléments de fichier
with open(file_items_file, 'w') as f:
    f.write('\n'.join(new_file_items))

print("Execution du script terminee.")

# Nettoyer les fichiers téléchargés et le dossier extrait
for file_link in new_files:
    file_path = os.path.join(download_folder, file_link)
    os.remove(file_path)

shutil.rmtree(extracted_folder)

print("Nettoyage termine.")
